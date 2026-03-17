<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLegalModalAcceptanceRequest;
use App\Models\LegalAcceptance;
use App\Models\User;
use App\Services\LegalAcceptanceEligibilityService;
use App\Support\LegalTerms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LegalAcceptanceController extends Controller
{
    public function storeModal(
        StoreLegalModalAcceptanceRequest $request,
        LegalAcceptanceEligibilityService $eligibilityService
    ): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user, 403, __('messages.user_not_authenticated'));
        if ($user->gym_id === null) {
            return back();
        }
        if ($user->demoSession()->active()->exists()) {
            return back();
        }
        if (! $eligibilityService->canUserAccept($user)) {
            return back()->with('error', 'Solo el dueño principal de la sede principal puede aceptar condiciones legales.');
        }
        if (! Schema::hasColumns('users', ['legal_accepted_at', 'legal_accepted_version'])) {
            return back()->with('error', 'Falta actualizar base de datos. Ejecuta: php artisan migrate');
        }
        if (! Schema::hasTable('legal_acceptances')) {
            return back()->with('error', 'Falta crear tabla legal_acceptances. Ejecuta: php artisan migrate');
        }
        if (! Schema::hasColumns('legal_acceptances', [
            'user_id',
            'gym_id',
            'accepted_via',
            'session_id',
            'source_url',
            'location_permission',
            'latitude',
            'longitude',
            'location_accuracy_m',
            'contract_code',
        ])) {
            return back()->with('error', 'Falta actualizar tabla legal_acceptances. Ejecuta: php artisan migrate');
        }

        $versión = LegalTerms::VERSION;
        $acceptedAt = now();
        $permission = (string) ($request->validated('location_permission') ?? 'skipped');
        $latitude = $permission === 'granted' ? $request->validated('latitude') : null;
        $longitude = $permission === 'granted' ? $request->validated('longitude') : null;
        $accuracy = $permission === 'granted' ? $request->validated('location_accuracy_m') : null;

        $acceptance = LegalAcceptance::query()
            ->where('user_id', (int) $user->id)
            ->where('document_key', 'all_terms')
            ->where('legal_version', $versión)
            ->first();

        if (! $acceptance) {
            $acceptance = LegalAcceptance::query()->create([
                'user_id' => (int) $user->id,
                'gym_id' => $user->gym_id ? (int) $user->gym_id : null,
                'full_name' => (string) ($user->name ?? ''),
                'email' => strtolower((string) ($user->email ?? '')),
                'document_key' => 'all_terms',
                'document_label' => 'Aceptación integral de condiciones legales',
                'legal_version' => $versión,
                'accepted' => true,
                'accepted_via' => 'login_modal',
                'session_id' => (string) ($request->session()->getId() ?? ''),
                'source_url' => mb_substr((string) url()->previous(), 0, 255),
                'location_permission' => $permission,
                'latitude' => $latitude !== null ? (float) $latitude : null,
                'longitude' => $longitude !== null ? (float) $longitude : null,
                'location_accuracy_m' => $accuracy !== null ? (float) $accuracy : null,
                'contract_code' => $this->buildContractCode($user, $versión, $acceptedAt),
                'accepted_at' => $acceptedAt,
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            ]);
        } else {
            $updates = [
                'accepted' => true,
                'accepted_via' => 'login_modal',
                'session_id' => (string) ($request->session()->getId() ?? ''),
                'source_url' => mb_substr((string) url()->previous(), 0, 255),
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            ];

            if (in_array($permission, ['granted', 'denied', 'error', 'unavailable'], true)) {
                $updates['location_permission'] = $permission;
            }
            if ($latitude !== null && $longitude !== null) {
                $updates['latitude'] = (float) $latitude;
                $updates['longitude'] = (float) $longitude;
                $updates['location_accuracy_m'] = $accuracy !== null ? (float) $accuracy : null;
            }
            if (trim((string) $acceptance->contract_code) === '') {
                $updates['contract_code'] = $this->buildContractCode($user, $versión, $acceptance->accepted_at ?? $acceptedAt);
            }
            if (! $acceptance->accepted_at) {
                $updates['accepted_at'] = $acceptedAt;
            }

            $acceptance->forceFill($updates)->save();
        }

        $user->forceFill([
            'legal_accepted_at' => $acceptance->accepted_at ?? $acceptedAt,
            'legal_accepted_version' => $versión,
        ])->save();

        return back()->with('status', 'Condiciones legales aceptadas y registradas.');
    }

    public function index(Request $request): View
    {
        $viewer = $request->user();
        abort_if(! $viewer || $viewer->gym_id !== null, 403, __('messages.user_not_authenticated'));

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'versión' => ['nullable', 'string', 'max:30'],
        ]);
        $queryText = trim((string) ($filters['q'] ?? ''));

        if (! Schema::hasTable('legal_acceptances')) {
            return view('superadmin.legal-acceptances.index', [
                'acceptances' => new LengthAwarePaginator([], 0, 25),
                'filters' => [
                    'q' => $queryText,
                    'from' => (string) ($filters['from'] ?? ''),
                    'to' => (string) ($filters['to'] ?? ''),
                    'versión' => (string) ($filters['versión'] ?? ''),
                ],
                'currentVersion' => LegalTerms::VERSION,
                'dbNotReady' => true,
            ]);
        }

        $query = LegalAcceptance::query()
            ->where('accepted', true)
            ->where(function ($builder): void {
                $builder
                    ->whereNull('gym_id')
                    ->orWhereHas('gym', fn ($gymQuery) => $gymQuery->withoutDemoSessions());
            })
            ->with(['user:id,name,email', 'gym:id,name'])
            ->orderByDesc('accepted_at');

        if ($queryText !== '') {
            $query->where(function ($builder) use ($queryText): void {
                $builder
                    ->where('full_name', 'like', '%'.$queryText.'%')
                    ->orWhere('email', 'like', '%'.$queryText.'%')
                    ->orWhere('contract_code', 'like', '%'.$queryText.'%')
                    ->orWhereHas('user', function ($userQuery) use ($queryText): void {
                        $userQuery
                            ->where('name', 'like', '%'.$queryText.'%')
                            ->orWhere('email', 'like', '%'.$queryText.'%');
                    });
            });
        }

        if (! empty($filters['from'])) {
            $query->where('accepted_at', '>=', (string) $filters['from'].' 00:00:00');
        }
        if (! empty($filters['to'])) {
            $query->where('accepted_at', '<=', (string) $filters['to'].' 23:59:59');
        }
        if (! empty($filters['versión'])) {
            $query->where('legal_version', (string) $filters['versión']);
        }

        return view('superadmin.legal-acceptances.index', [
            'acceptances' => $query->paginate(25)->withQueryString(),
            'filters' => [
                'q' => $queryText,
                'from' => (string) ($filters['from'] ?? ''),
                'to' => (string) ($filters['to'] ?? ''),
                'versión' => (string) ($filters['versión'] ?? ''),
            ],
            'currentVersion' => LegalTerms::VERSION,
            'dbNotReady' => false,
        ]);
    }

    public function contractPdf(Request $request, LegalAcceptance $acceptance): Response|RedirectResponse
    {
        $viewer = $request->user();
        abort_if(! $viewer || $viewer->gym_id !== null, 403, __('messages.user_not_authenticated'));
        if (! Schema::hasTable('legal_acceptances')) {
            return redirect()
                ->route('superadmin.legal-acceptances.index')
                ->with('error', 'Falta crear tabla legal_acceptances. Ejecuta: php artisan migrate');
        }
        if ($acceptance->gym()->whereHas('demoSession')->exists()) {
            abort(404, 'La aceptación legal no está disponible.');
        }
        abort_if(! $acceptance->accepted, 404, 'La aceptación legal no está confirmada.');

        $terms = LegalTerms::orderedDocuments();
        $contractCode = trim((string) $acceptance->contract_code);
        if ($contractCode === '') {
            $contractCode = $this->buildContractCode(
                $acceptance->user ?: new User(['id' => 0, 'email' => $acceptance->email]),
                (string) $acceptance->legal_version,
                $acceptance->accepted_at ?? now()
            );
        }

        $pdf = Pdf::loadView('superadmin.legal-acceptances.contract-pdf', [
            'acceptance' => $acceptance,
            'contractCode' => $contractCode,
            'terms' => $terms,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        $filename = 'contrato_legal_'.$contractCode.'.pdf';

        return $pdf->stream($filename);
    }

    private function buildContractCode(User $user, string $versión, mixed $acceptedAt): string
    {
        $seed = implode('|', [
            (string) ($user->id ?? 0),
            strtolower((string) ($user->email ?? '')),
            $versión,
            (string) (is_object($acceptedAt) && method_exists($acceptedAt, 'format') ? $acceptedAt->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s')),
            (string) now()->valueOf(),
        ]);

        return strtoupper(substr(hash('sha256', $seed), 0, 32));
    }
}
