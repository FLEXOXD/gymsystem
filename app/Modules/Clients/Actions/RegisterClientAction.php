<?php

namespace App\Modules\Clients\Actions;

use App\Models\Client;
use App\Models\Membership;
use App\Models\Plan;
use App\Modules\Clients\Services\ClientMembershipDomainService;
use App\Services\CashSessionService;
use App\Services\PromotionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class RegisterClientAction
{
    public function __construct(
        private readonly CashSessionService $cashSessionService,
        private readonly PromotionService $promotionService,
        private readonly ClientMembershipDomainService $membershipDomainService
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function execute(
        int $gymId,
        int $userId,
        array $data,
        bool $canManagePromotions,
        ?string $photoPath = null,
        bool $canManageClientAccounts = false
    ): Client {
        $startsMembership = (bool) ($data['start_membership'] ?? false);
        $requestedAppUsername = mb_strtolower(trim((string) ($data['app_username'] ?? '')));
        $requestedAppPassword = trim((string) ($data['app_password'] ?? ''));
        $createAppAccount = $canManageClientAccounts
            && (bool) ($data['create_app_account'] ?? false)
            && $requestedAppUsername !== ''
            && $requestedAppPassword !== '';

        return DB::transaction(function () use ($gymId, $userId, $data, $canManagePromotions, $photoPath, $startsMembership, $createAppAccount, $requestedAppUsername, $requestedAppPassword): Client {
            $client = Client::query()->create([
                'gym_id' => $gymId,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'document_number' => $data['document_number'],
                'app_username' => $createAppAccount ? $requestedAppUsername : null,
                'app_password' => $createAppAccount ? Hash::make($requestedAppPassword) : null,
                'phone' => $data['phone'] ?? null,
                'photo_path' => $photoPath,
                'gender' => $data['gender'] ?? 'neutral',
                'status' => 'inactive',
            ]);

            if (! $startsMembership) {
                return $client;
            }

            $plan = Plan::query()
                ->forGym($gymId)
                ->active()
                ->select(['id', 'name', 'duration_days', 'duration_unit', 'duration_months', 'price'])
                ->findOrFail((int) $data['plan_id']);

            $pricing = $this->promotionService->resolveForSale(
                gymId: $gymId,
                plan: $plan,
                promotionId: $canManagePromotions ? ($data['promotion_id'] ?? null) : null,
                date: $data['membership_starts_at'] ?? now()->toDateString()
            );

            if (! empty($data['promotion_id']) && ! $pricing['promotion']) {
                throw new RuntimeException('La promocion seleccionada no aplica para este plan, fecha o ya alcanzo su limite.');
            }

            $startsAt = Carbon::parse((string) $data['membership_starts_at'])->startOfDay();
            $membershipWindow = $this->membershipDomainService->resolveMembershipWindow(
                startsAt: $startsAt,
                plan: $plan,
                bonusDays: (int) $pricing['bonus_days']
            );

            $membership = Membership::query()->create([
                'gym_id' => $gymId,
                'client_id' => $client->id,
                'plan_id' => $plan->id,
                'price' => $pricing['final_price'],
                'promotion_id' => $pricing['promotion']?->id,
                'promotion_name' => $pricing['promotion']?->name,
                'promotion_type' => $pricing['promotion']?->type,
                'promotion_value' => $pricing['promotion']?->value,
                'discount_amount' => $pricing['discount_amount'],
                'bonus_days' => $pricing['bonus_days'],
                'starts_at' => $startsAt->toDateString(),
                'ends_at' => $membershipWindow['ends_at']->toDateString(),
                'status' => $membershipWindow['status'],
            ]);

            $membershipPrice = round((float) $pricing['final_price'], 2);
            $amountPaid = round((float) $data['amount_paid'], 2);
            $amountPaid = min($amountPaid, $membershipPrice);

            if ($amountPaid > 0) {
                $description = $this->membershipDomainService->buildMembershipCashDescription(
                    membershipId: (int) $membership->id,
                    planName: (string) $plan->name,
                    basePrice: (float) $pricing['base_price'],
                    promotion: $pricing['promotion']
                );

                $this->cashSessionService->addMovement(
                    gymId: $gymId,
                    userId: $userId,
                    type: 'income',
                    amount: $amountPaid,
                    method: (string) $data['payment_method'],
                    membershipId: $membership->id,
                    description: $description
                );
            }

            if ($pricing['promotion']) {
                $pricing['promotion']->increment('times_used');
            }

            $client->update([
                'status' => $membershipWindow['status'] === 'active' ? 'active' : 'inactive',
            ]);

            return $client;
        });
    }
}
