<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuperAdminBranchRequest;
use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\User;
use App\Services\BranchProvisioningService;
use App\Services\PlanAccessService;
use App\Support\GymLocationCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

class SuperAdminBranchController extends Controller
{
    public function __construct(
        private readonly PlanAccessService $planAccessService,
        private readonly BranchProvisioningService $branchProvisioningService
    ) {
    }

    public function index(): View
    {
        $allGyms = Gym::query()
            ->withoutDemoSessions()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
                'address',
                'address_state',
                'address_city',
                'address_line',
            ]);

        $allGymIds = $allGyms
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
        $hubAccessByGym = $this->planAccessService->canForGyms($allGymIds, 'multi_branch');

        $hubGyms = $allGyms
            ->filter(fn (Gym $gym): bool => (bool) ($hubAccessByGym[(int) $gym->id] ?? false))
            ->values();
        $hubGymAdminDomains = [];
        if ($hubGyms->isNotEmpty()) {
            $hubGymIds = $hubGyms->pluck('id')->map(static fn ($id): int => (int) $id)->all();
            $hubGymAdminDomains = User::query()
                ->whereIn('gym_id', $hubGymIds)
                ->orderBy('id')
                ->get(['gym_id', 'email'])
                ->groupBy('gym_id')
                ->map(function ($users): string {
                    $email = strtolower(trim((string) ($users->first()?->email ?? '')));
                    if ($email === '' || ! str_contains($email, '@')) {
                        return 'gymsystem.app';
                    }

                    [, $domain] = explode('@', $email, 2);
                    $domain = strtolower(trim((string) $domain));

                    return $domain !== '' ? $domain : 'gymsystem.app';
                })
                ->toArray();
        }

        $links = GymBranchLink::query()
            ->with([
                'hubGym:id,name,slug,address,address_state,address_city,address_line',
                'branchGym:id,name,slug,address,address_state,address_city,address_line',
                'createdBy:id,name,email',
            ])
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $kpis = [
            'total_links' => GymBranchLink::query()->count(),
            'total_hubs' => GymBranchLink::query()->distinct()->count('hub_gym_id'),
            'total_branches' => GymBranchLink::query()->distinct()->count('branch_gym_id'),
        ];

        return view('superadmin.branches', [
            'hubGyms' => $hubGyms,
            'links' => $links,
            'kpis' => $kpis,
            'locationCatalog' => GymLocationCatalog::catalog(),
            'defaultBranchCountry' => 'ec',
            'branchPlanOptions' => [
                'basico' => 'Básico',
                'profesional' => 'Profesional',
                'premium' => 'Premium',
            ],
            'hubGymAdminDomains' => $hubGymAdminDomains,
        ]);
    }

    public function store(StoreSuperAdminBranchRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $hubGymId = (int) $data['hub_gym_id'];

        $hubGym = Gym::query()
            ->withoutDemoSessions()
            ->select(['id', 'name', 'slug'])
            ->find($hubGymId);

        if (! $hubGym) {
            return redirect()
                ->route('superadmin.branches.index')
                ->withErrors(['hub_gym_id' => 'Selecciona una sede principal válida.'])
                ->withInput();
        }

        if (! $this->planAccessService->canForGym($hubGymId, 'multi_branch')) {
            return redirect()
                ->route('superadmin.branches.index')
                ->withErrors(['hub_gym_id' => 'La sede principal seleccionada no tiene habilitado el plan sucursales.'])
                ->withInput();
        }

        try {
            $link = $this->branchProvisioningService->createBranchForHub(
                hubGymId: $hubGymId,
                branchName: (string) $data['branch_name'],
                branchPhone: $data['branch_phone'] ?? null,
                countryCode: (string) $data['branch_country'],
                state: (string) $data['branch_state'],
                city: (string) $data['branch_city'],
                addressLine: $data['branch_address_line'] ?? null,
                branchPlanKey: (string) $data['branch_plan_key'],
                branchAdminName: (string) $data['branch_admin_name'],
                branchAdminPassword: (string) $data['branch_admin_password'],
                cashManagedByHub: true,
                createdByUserId: (int) ($request->user()?->id ?? 0)
            );
        } catch (InvalidArgumentException $exception) {
            $message = $exception->getMessage();
            $errorField = str_contains(mb_strtolower($message), 'correo')
                ? 'branch_admin_email'
                : 'branch_name';

            return redirect()
                ->route('superadmin.branches.index')
                ->withErrors([$errorField => $message])
                ->withInput();
        }

        return redirect()
            ->route('superadmin.branches.index')
            ->with('status', 'Sucursal creada y vinculada correctamente. ID enlace: '.$link->id.'.');
    }

    public function destroy(int $link): RedirectResponse
    {
        $linkModel = GymBranchLink::query()->findOrFail($link);
        $linkModel->delete();

        return redirect()
            ->route('superadmin.branches.index')
            ->with('status', 'Sucursal desvinculada correctamente.');
    }
}
