<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\RemoteScanEvent;
use App\Models\RemoteScanSession;
use App\Services\RemoteScanService;
use App\Support\ActiveGymContext;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RemoteScanController extends Controller
{
    public function __construct(
        private readonly RemoteScanService $remoteScanService
    ) {
    }

    public function createSession(Request $request, string $contextGym): JsonResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return response()->json([
                'ok' => false,
                'message' => 'Selecciona una sede especifica para escanear en tiempo real.',
            ], 422);
        }

        $actor = $request->user();
        abort_unless($actor, 403);

        $data = $request->validate([
            'context' => ['required', 'in:products,sales'],
            'force' => ['nullable', 'boolean'],
        ]);

        $gymId = ActiveGymContext::id($request);
        abort_if($gymId <= 0, 403);
        $sessionExpiry = $this->monthlySessionExpiry();

        $session = $this->remoteScanService->createSession(
            gymId: $gymId,
            actor: $actor,
            context: (string) $data['context'],
            forceNew: (bool) ($data['force'] ?? false),
            expiresAt: $sessionExpiry
        );
        $signedExpiry = $session->expires_at?->copy() ?? $sessionExpiry;

        $mobilePath = URL::temporarySignedRoute(
            'remote-scanner.mobile',
            $signedExpiry,
            [
                'contextGym' => $contextGym,
                'channel' => $session->channel_token,
            ],
            absolute: false
        );
        $mobileUrl = url($mobilePath);

        $streamUrl = route('remote-scanner.stream', [
            'contextGym' => $contextGym,
            'channel' => $session->channel_token,
        ]);

        $closeUrl = route('remote-scanner.close', [
            'contextGym' => $contextGym,
            'channel' => $session->channel_token,
        ]);

        $qrSvg = (string) QrCode::format('svg')
            ->size(220)
            ->margin(1)
            ->generate($mobileUrl);

        return response()->json([
            'ok' => true,
            'session' => [
                'id' => (int) $session->id,
                'channel' => (string) $session->channel_token,
                'context' => (string) $session->context,
                'expires_at' => $session->expires_at?->toIso8601String(),
                'expires_label' => $session->expires_at?->format('Y-m-d H:i'),
                'rotation_note' => 'Este enlace se actualiza el primer día de cada mes.',
                'stream_url' => $streamUrl,
                'mobile_url' => $mobileUrl,
                'close_url' => $closeUrl,
                'qr_svg' => $qrSvg,
                'short_code' => Str::upper(substr(str_replace('-', '', (string) $session->channel_token), 0, 6)),
            ],
        ]);
    }

    public function stream(Request $request, string $contextGym, string $channel): StreamedResponse
    {
        $actor = $request->user();
        abort_unless($actor, 403);

        $session = $this->resolveOwnedSession($request, $channel);
        $lastEventId = max(0, (int) $request->header('Last-Event-ID', 0));

        return response()->stream(function () use ($session, $lastEventId): void {
            $cursor = $lastEventId;
            $startedAt = microtime(true);

            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', '0');
            @set_time_limit(0);

            echo "retry: 1000\n";
            echo "event: connected\n";
            echo 'data: '.json_encode([
                'session' => (string) $session->channel_token,
                'expires_at' => $session->expires_at?->toIso8601String(),
            ])."\n\n";
            @ob_flush();
            @flush();

            while (! connection_aborted() && (microtime(true) - $startedAt) < 25) {
                $session->refresh();

                if ($session->isExpired()) {
                    echo "event: close\n";
                    echo 'data: '.json_encode([
                        'reason' => 'expired',
                    ])."\n\n";
                    @ob_flush();
                    @flush();
                    break;
                }

                $events = RemoteScanEvent::query()
                    ->forSession((int) $session->id)
                    ->where('id', '>', $cursor)
                    ->orderBy('id')
                    ->get();

                foreach ($events as $event) {
                    $cursor = (int) $event->id;
                    echo 'id: '.$event->id."\n";
                    echo "event: scan\n";
                    echo 'data: '.json_encode([
                        'id' => (int) $event->id,
                        'code' => (string) $event->code,
                        'source' => (string) $event->source,
                        'created_at' => $event->created_at?->toIso8601String(),
                    ])."\n\n";
                    @ob_flush();
                    @flush();
                }

                if ($events->isEmpty()) {
                    echo "event: ping\n";
                    echo 'data: {"ok":true}'."\n\n";
                    @ob_flush();
                    @flush();
                }

                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function close(Request $request, string $contextGym, string $channel): JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor, 403);

        $session = $this->resolveOwnedSession($request, $channel);
        $this->remoteScanService->close($session);

        return response()->json(['ok' => true]);
    }

    public function mobile(Request $request, string $contextGym, string $channel): View|Response
    {
        $session = $this->resolvePublicSession($contextGym, $channel);
        if ($session->isExpired()) {
            return response()->view('remote-scanner.expired', [
                'session' => $session,
                'contextGym' => $contextGym,
            ], 410);
        }

        $capturePath = URL::temporarySignedRoute(
            'remote-scanner.capture',
            $session->expires_at?->copy() ?? $this->monthlySessionExpiry(),
            [
                'contextGym' => $contextGym,
                'channel' => $session->channel_token,
            ],
            absolute: false
        );
        $captureUrl = url($capturePath);

        return view('remote-scanner.mobile', [
            'session' => $session,
            'captureUrl' => $captureUrl,
            'contextGym' => $contextGym,
        ]);
    }

    public function capture(Request $request, string $contextGym, string $channel): JsonResponse
    {
        $session = $this->resolvePublicSession($contextGym, $channel);

        if ($session->isExpired()) {
            return response()->json([
                'ok' => false,
                'message' => 'La sesion de escaneo ya expiro.',
            ], 410);
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'min:4', 'max:120'],
            'source' => ['nullable', 'string', 'max:20'],
        ]);

        $event = $this->remoteScanService->pushCode(
            session: $session,
            code: (string) $data['code'],
            source: trim((string) ($data['source'] ?? 'camera')) !== '' ? trim((string) $data['source']) : 'camera',
            meta: [
                'ip' => (string) $request->ip(),
            ]
        );

        return response()->json([
            'ok' => true,
            'event_id' => (int) $event->id,
            'code' => (string) $event->code,
        ]);
    }

    private function resolveOwnedSession(Request $request, string $channel): RemoteScanSession
    {
        $gymId = ActiveGymContext::id($request);
        abort_if($gymId <= 0, 403);

        $actor = $request->user();
        abort_unless($actor, 403);

        $session = RemoteScanSession::query()
            ->where('channel_token', $channel)
            ->where('gym_id', $gymId)
            ->where('created_by', (int) $actor->id)
            ->firstOrFail();

        return $session;
    }

    private function resolvePublicSession(string $contextGym, string $channel): RemoteScanSession
    {
        $gym = Gym::query()
            ->withoutDemoSessions()
            ->select(['id', 'slug'])
            ->whereRaw('LOWER(slug) = ?', [mb_strtolower(trim($contextGym))])
            ->firstOrFail();

        $session = RemoteScanSession::query()
            ->with('gym:id,name,slug')
            ->where('channel_token', $channel)
            ->where('gym_id', (int) $gym->id)
            ->firstOrFail();

        return $session;
    }

    private function monthlySessionExpiry(): Carbon
    {
        return now()->copy()->startOfMonth()->addMonth()->startOfDay();
    }
}
