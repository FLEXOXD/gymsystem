<?php

namespace App\Http\Controllers;

use Carbon\CarbonInterface;
use App\Models\SupportChatConversation;
use App\Models\SupportChatMessage;
use App\Models\User;
use App\Services\SupportChatLifecycleService;
use App\Services\SupportChatPresenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SuperAdminSupportChatController extends Controller
{
    public function __construct(
        private readonly SupportChatPresenceService $presenceService,
        private readonly SupportChatLifecycleService $lifecycleService,
    ) {
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $this->lifecycleService->runInactivitySweep();

        $user = $request->user();
        if (! $user instanceof User || ! $user->hasRole(User::ROLE_SUPERADMIN)) {
            abort(403);
        }

        $this->presenceService->touchSuperAdmin($user);

        return response()->json([
            'ok' => true,
            'online' => true,
        ]);
    }

    public function unreadCountJson(Request $request): JsonResponse
    {
        $this->lifecycleService->runInactivitySweep();

        $user = $request->user();
        if (! $user instanceof User || ! $user->hasRole(User::ROLE_SUPERADMIN)) {
            abort(403);
        }

        return response()->json([
            'ok' => true,
            'unread' => $this->unreadCount(),
        ]);
    }

    public function state(Request $request, SupportChatConversation $conversation): JsonResponse
    {
        $this->lifecycleService->runInactivitySweep((int) $conversation->id);
        $this->markConversationAsRead($conversation);

        return response()->json($this->buildStatePayload($request, $conversation));
    }

    public function reply(Request $request, SupportChatConversation $conversation): JsonResponse|RedirectResponse
    {
        $this->lifecycleService->runInactivitySweep((int) $conversation->id);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1400'],
        ]);

        $user = $request->user();
        if (! $user instanceof User) {
            abort(403);
        }

        $attributes = [
            'conversation_id' => (int) $conversation->id,
            'sender_type' => SupportChatMessage::SENDER_SUPERADMIN,
            'sender_user_id' => (int) $user->id,
            'message' => trim((string) $data['message']),
        ];

        if (Schema::hasColumn('support_chat_messages', 'sender_label')) {
            $attributes['sender_label'] = trim((string) $user->name);
        }
        if (Schema::hasColumn('support_chat_messages', 'message_type')) {
            $attributes['message_type'] = 'text';
        }
        if (Schema::hasColumn('support_chat_messages', 'read_by_superadmin_at')) {
            $attributes['read_by_superadmin_at'] = now();
        }

        SupportChatMessage::query()->create($attributes);

        $conversation->forceFill([
            'status' => SupportChatConversation::STATUS_ACTIVE,
            'representative_requested_at' => $conversation->representative_requested_at ?? now(),
            'representative_joined_at' => $conversation->representative_joined_at ?? now(),
            'closed_at' => null,
            'last_message_at' => now(),
        ])->save();

        $this->markConversationAsRead($conversation);
        $this->presenceService->touchSuperAdmin($user);

        if ($request->expectsJson()) {
            return response()->json(array_merge(
                $this->buildStatePayload($request, $conversation),
                ['notice' => 'Respuesta enviada al chat de soporte.']
            ));
        }

        return back()->with('status', 'Respuesta enviada al chat de soporte.');
    }

    public function updateStatus(Request $request, SupportChatConversation $conversation): JsonResponse|RedirectResponse
    {
        $this->lifecycleService->runInactivitySweep((int) $conversation->id);

        $data = $request->validate([
            'status' => ['required', 'in:bot,waiting_agent,active,closed'],
        ]);

        $nextStatus = (string) $data['status'];
        $forceFill = [
            'status' => $nextStatus,
            'closed_at' => $nextStatus === SupportChatConversation::STATUS_CLOSED ? now() : null,
        ];

        if (in_array($nextStatus, [SupportChatConversation::STATUS_WAITING_AGENT, SupportChatConversation::STATUS_ACTIVE], true)) {
            $forceFill['representative_requested_at'] = $conversation->representative_requested_at ?? now();
        }
        if ($nextStatus === SupportChatConversation::STATUS_ACTIVE) {
            $forceFill['representative_joined_at'] = $conversation->representative_joined_at ?? now();
        }

        $conversation->forceFill($forceFill)->save();

        if ($request->expectsJson()) {
            return response()->json(array_merge(
                $this->buildStatePayload($request, $conversation),
                ['notice' => 'Estado de conversacion actualizado.']
            ));
        }

        return back()->with('status', 'Estado de conversacion actualizado.');
    }

    public function markRead(Request $request, SupportChatConversation $conversation): RedirectResponse
    {
        $this->lifecycleService->runInactivitySweep((int) $conversation->id);
        $this->markConversationAsRead($conversation);

        return back()->with('status', 'Conversacion marcada como leida.');
    }

    public function finalize(Request $request, SupportChatConversation $conversation): JsonResponse|RedirectResponse
    {
        $conversationIds = $this->resolveConversationIdsForImmediatePurge($conversation);
        $this->deleteConversationsImmediately($conversationIds);

        $query = $request->only(['status', 'q', 'page', 'support_status', 'support_q', 'support_page']);
        unset($query['support']);
        $redirectUrl = route('superadmin.inbox.index', $query);
        $notice = 'Conversacion finalizada y eliminada con su historial.';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'deleted' => true,
                'deleted_conversation_ids' => $conversationIds,
                'notice' => $notice,
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect()
            ->route('superadmin.inbox.index', $query)
            ->with('status', $notice);
    }

    public function unreadCount(): int
    {
        if (! Schema::hasTable('support_chat_conversations') || ! Schema::hasTable('support_chat_messages')) {
            return 0;
        }

        if (! Schema::hasColumn('support_chat_messages', 'read_by_superadmin_at')) {
            return 0;
        }

        try {
            return SupportChatConversation::query()
                ->whereIn('status', [
                    SupportChatConversation::STATUS_BOT,
                    SupportChatConversation::STATUS_WAITING_AGENT,
                    SupportChatConversation::STATUS_ACTIVE,
                ])
                ->whereHas('messages', function ($query): void {
                    $query
                        ->whereIn('sender_type', [SupportChatMessage::SENDER_VISITOR, SupportChatMessage::SENDER_GYM])
                        ->whereNull('read_by_superadmin_at');
                })
                ->count();
        } catch (Throwable $exception) {
            report($exception);

            return 0;
        }
    }

    private function markConversationAsRead(SupportChatConversation $conversation): void
    {
        if (! Schema::hasColumn('support_chat_messages', 'read_by_superadmin_at')) {
            return;
        }

        $conversation->messages()
            ->whereIn('sender_type', [SupportChatMessage::SENDER_VISITOR, SupportChatMessage::SENDER_GYM])
            ->whereNull('read_by_superadmin_at')
            ->update(['read_by_superadmin_at' => now()]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildStatePayload(Request $request, SupportChatConversation $conversation): array
    {
        $conversation = $conversation->fresh([
            'gym:id,name,logo_path',
            'initiatedBy:id,name,email',
            'messages' => static fn ($query) => $query->orderBy('id')->with('senderUser:id,name'),
        ]) ?? $conversation;

        $panelTimezone = $this->resolvePanelTimezone($request);
        $activeRepresentative = $this->presenceService->activeRepresentative();

        return [
            'ok' => true,
            'representative_online' => $activeRepresentative !== null,
            'representative_name' => trim((string) ($activeRepresentative['name'] ?? 'SuperAdmin')),
            'conversation' => [
                'id' => (int) $conversation->id,
                'status' => (string) $conversation->status,
                'status_label' => $conversation->statusLabel(),
                'display_name' => $conversation->displayName(),
                'source_label' => $conversation->sourceLabel(),
                'requester_label' => $conversation->requesterLabel(),
                'visitor_email' => trim((string) $conversation->visitor_email),
                'visitor_name' => trim((string) $conversation->visitor_name),
                'is_closed' => (string) $conversation->status === SupportChatConversation::STATUS_CLOSED,
                'minutes_until_purge' => $this->minutesUntilPurge($conversation),
            ],
            'messages' => $conversation->messages
                ->map(static function (SupportChatMessage $message) use ($panelTimezone): array {
                    $senderType = (string) ($message->sender_type ?? '');
                    $senderLabel = trim((string) ($message->sender_label ?? ''));

                    if ($senderLabel === '') {
                        $senderLabel = match ($senderType) {
                            SupportChatMessage::SENDER_VISITOR => 'Visitante',
                            SupportChatMessage::SENDER_GYM => 'Gimnasio',
                            SupportChatMessage::SENDER_SUPERADMIN => trim((string) ($message->senderUser?->name ?? 'SuperAdmin')),
                            SupportChatMessage::SENDER_BOT => 'Bot de soporte',
                            default => 'Sistema',
                        };
                    }

                    return [
                        'id' => (int) $message->id,
                        'sender_type' => $senderType,
                        'sender_label' => $senderLabel,
                        'message' => (string) $message->message,
                        'mine' => $senderType === SupportChatMessage::SENDER_SUPERADMIN,
                        'is_system' => in_array($senderType, [SupportChatMessage::SENDER_SYSTEM, SupportChatMessage::SENDER_BOT], true),
                        'created_at' => $message->created_at instanceof CarbonInterface
                            ? $message->created_at->copy()->timezone($panelTimezone)->format('d/m/Y H:i')
                            : '',
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    private function minutesUntilPurge(SupportChatConversation $conversation): ?int
    {
        if ((string) $conversation->status !== SupportChatConversation::STATUS_CLOSED || ! $conversation->closed_at) {
            return null;
        }

        $purgeAfterMinutes = max(1, (int) config('support_chat.inactivity.close_after_minutes', 15));

        return max(0, now()->diffInMinutes($conversation->closed_at->copy()->addMinutes($purgeAfterMinutes), false));
    }

    private function resolvePanelTimezone(Request $request): string
    {
        $panelTimezone = trim((string) ($request->user()?->timezone ?? ''));
        if (
            $panelTimezone === ''
            || $panelTimezone === 'UTC'
            || ! in_array($panelTimezone, timezone_identifiers_list(), true)
        ) {
            return 'America/Guayaquil';
        }

        return $panelTimezone;
    }

    /**
     * @return list<int>
     */
    private function resolveConversationIdsForImmediatePurge(SupportChatConversation $conversation): array
    {
        $channel = trim((string) $conversation->channel);
        $ids = [];

        if ($channel === SupportChatConversation::CHANNEL_GYM_PANEL && (int) $conversation->gym_id > 0) {
            $ids = SupportChatConversation::query()
                ->where('channel', SupportChatConversation::CHANNEL_GYM_PANEL)
                ->where('gym_id', (int) $conversation->gym_id)
                ->pluck('id')
                ->map(static fn (mixed $id): int => (int) $id)
                ->filter(static fn (int $id): bool => $id > 0)
                ->values()
                ->all();
        } elseif ($channel === SupportChatConversation::CHANNEL_LANDING) {
            $visitorEmail = trim((string) $conversation->visitor_email);
            $visitorGymName = trim((string) $conversation->visitor_gym_name);

            $query = SupportChatConversation::query()
                ->where('channel', SupportChatConversation::CHANNEL_LANDING);

            if ($visitorEmail !== '') {
                $query->where('visitor_email', $visitorEmail);
            } elseif ($visitorGymName !== '') {
                $query->where('visitor_gym_name', $visitorGymName);
            } else {
                $query->whereKey((int) $conversation->id);
            }

            $ids = $query
                ->pluck('id')
                ->map(static fn (mixed $id): int => (int) $id)
                ->filter(static fn (int $id): bool => $id > 0)
                ->values()
                ->all();
        }

        $ids[] = (int) $conversation->id;

        return collect($ids)
            ->map(static fn (mixed $id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<int>  $conversationIds
     */
    private function deleteConversationsImmediately(array $conversationIds): void
    {
        $ids = collect($conversationIds)
            ->map(static fn (mixed $id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        if ($ids === []) {
            return;
        }

        DB::transaction(static function () use ($ids): void {
            SupportChatMessage::query()
                ->whereIn('conversation_id', $ids)
                ->delete();

            SupportChatConversation::query()
                ->whereIn('id', $ids)
                ->delete();
        });
    }
}
