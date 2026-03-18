<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscription_charge_events')) {
            Schema::create('subscription_charge_events', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
                $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
                $table->foreignId('plan_template_id')->nullable()->constrained('superadmin_plan_templates')->nullOnDelete();
                $table->foreignId('promotion_template_id')->nullable()->constrained('superadmin_promotion_templates')->nullOnDelete();
                $table->string('plan_key', 40)->nullable();
                $table->string('plan_name', 120);
                $table->string('event_type', 30)->default('renewal');
                $table->string('payment_method', 40)->nullable();
                $table->unsignedInteger('billing_cycles')->default(1);
                $table->decimal('base_monthly_price', 10, 2)->default(0);
                $table->decimal('effective_monthly_price', 10, 2)->default(0);
                $table->decimal('base_total', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('total_paid', 10, 2)->default(0);
                $table->unsignedInteger('bonus_days')->default(0);
                $table->timestamp('charged_at');
                $table->timestamps();

                $table->index(['gym_id', 'charged_at']);
                $table->index(['subscription_id', 'charged_at']);
                $table->index(['plan_key', 'charged_at']);
                $table->index(['event_type', 'charged_at']);
            });
        }

        $this->backfillCurrentSubscriptionCycles();
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_charge_events');
    }

    private function backfillCurrentSubscriptionCycles(): void
    {
        if (! Schema::hasTable('subscriptions') || ! Schema::hasTable('gyms')) {
            return;
        }

        $query = DB::table('subscriptions')
            ->leftJoin('superadmin_plan_templates as templates', 'templates.id', '=', 'subscriptions.plan_template_id')
            ->leftJoin('subscription_charge_events as charge_events', 'charge_events.subscription_id', '=', 'subscriptions.id')
            ->select([
                'subscriptions.id as subscription_id',
                'subscriptions.id as chunk_id',
                'subscriptions.gym_id',
                'subscriptions.plan_template_id',
                'subscriptions.plan_key',
                'subscriptions.plan_name',
                'subscriptions.price',
                'subscriptions.starts_at',
                'subscriptions.ends_at',
                'subscriptions.last_payment_method',
                'subscriptions.sucursales_base_price',
                'subscriptions.is_branch_managed',
                'templates.price as template_price',
                'templates.duration_days as template_duration_days',
                'templates.duration_months as template_duration_months',
                'charge_events.id as existing_charge_event_id',
            ])
            ->whereNull('charge_events.id')
            ->where(function ($subQuery): void {
                $subQuery
                    ->where('subscriptions.is_branch_managed', false)
                    ->orWhereNull('subscriptions.is_branch_managed');
            })
            ->orderBy('subscriptions.id');

        $query->chunkById(200, function ($rows): void {
            $payload = [];

            foreach ($rows as $row) {
                if ($row->existing_charge_event_id !== null) {
                    continue;
                }

                $billingCycles = $this->estimateBillingCycles(
                    startsAt: $row->starts_at,
                    endsAt: $row->ends_at,
                    templateDurationDays: $row->template_duration_days,
                    templateDurationMonths: $row->template_duration_months
                );

                $effectiveMonthlyPrice = max(0, round((float) ($row->price ?? 0), 2));
                $baseMonthlyPrice = $this->resolveBaseMonthlyPrice(
                    planKey: (string) ($row->plan_key ?? ''),
                    effectiveMonthlyPrice: $effectiveMonthlyPrice,
                    templatePrice: $row->template_price,
                    sucursalesBasePrice: $row->sucursales_base_price
                );
                $baseTotal = round($baseMonthlyPrice * $billingCycles, 2);
                $totalPaid = round($effectiveMonthlyPrice * $billingCycles, 2);
                $discountAmount = max(0, round($baseTotal - $totalPaid, 2));
                $bonusDays = $this->estimateBonusDays(
                    startsAt: $row->starts_at,
                    endsAt: $row->ends_at,
                    billingCycles: $billingCycles,
                    templateDurationDays: $row->template_duration_days,
                    templateDurationMonths: $row->template_duration_months
                );

                $payload[] = [
                    'gym_id' => (int) $row->gym_id,
                    'subscription_id' => (int) $row->subscription_id,
                    'plan_template_id' => $row->plan_template_id !== null ? (int) $row->plan_template_id : null,
                    'promotion_template_id' => null,
                    'plan_key' => $row->plan_key !== null ? (string) $row->plan_key : null,
                    'plan_name' => trim((string) ($row->plan_name ?? 'Plan comercial')) !== '' ? (string) $row->plan_name : 'Plan comercial',
                    'event_type' => 'backfill',
                    'payment_method' => $row->last_payment_method !== null ? (string) $row->last_payment_method : null,
                    'billing_cycles' => $billingCycles,
                    'base_monthly_price' => $baseMonthlyPrice,
                    'effective_monthly_price' => $effectiveMonthlyPrice,
                    'base_total' => $baseTotal,
                    'discount_amount' => $discountAmount,
                    'total_paid' => $totalPaid,
                    'bonus_days' => $bonusDays,
                    'charged_at' => $this->resolveChargedAt($row->starts_at),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if ($payload !== []) {
                DB::table('subscription_charge_events')->insert($payload);
            }
        }, 'subscriptions.id', 'chunk_id');
    }

    private function resolveBaseMonthlyPrice(
        string $planKey,
        float $effectiveMonthlyPrice,
        mixed $templatePrice,
        mixed $sucursalesBasePrice
    ): float {
        $normalizedPlanKey = strtolower(trim($planKey));
        $resolvedTemplatePrice = is_numeric($templatePrice) ? round((float) $templatePrice, 2) : null;
        $resolvedSucursalesBasePrice = is_numeric($sucursalesBasePrice) ? round((float) $sucursalesBasePrice, 2) : null;

        if ($normalizedPlanKey === 'sucursales' && $resolvedSucursalesBasePrice !== null && $resolvedSucursalesBasePrice > 0) {
            return $resolvedSucursalesBasePrice;
        }

        if ($resolvedTemplatePrice !== null && $resolvedTemplatePrice > 0) {
            return $resolvedTemplatePrice;
        }

        return $effectiveMonthlyPrice;
    }

    private function estimateBillingCycles(
        mixed $startsAt,
        mixed $endsAt,
        mixed $templateDurationDays,
        mixed $templateDurationMonths
    ): int {
        $coverageDays = $this->coverageDays($startsAt, $endsAt);
        $baseDurationDays = $this->baseDurationDays($templateDurationDays, $templateDurationMonths);

        if ($coverageDays <= $baseDurationDays) {
            return 1;
        }

        $ratio = $coverageDays / max(1, $baseDurationDays);
        $rounded = max(1, (int) round($ratio));

        if (abs($ratio - $rounded) <= 0.35) {
            return $rounded;
        }

        return max(1, (int) floor($ratio));
    }

    private function estimateBonusDays(
        mixed $startsAt,
        mixed $endsAt,
        int $billingCycles,
        mixed $templateDurationDays,
        mixed $templateDurationMonths
    ): int {
        $coverageDays = $this->coverageDays($startsAt, $endsAt);
        $baseDurationDays = $this->baseDurationDays($templateDurationDays, $templateDurationMonths);
        $expectedCoverageDays = max(1, $baseDurationDays * max(1, $billingCycles));

        return max(0, $coverageDays - $expectedCoverageDays);
    }

    private function coverageDays(mixed $startsAt, mixed $endsAt): int
    {
        try {
            $start = Carbon::parse((string) $startsAt)->startOfDay();
            $end = Carbon::parse((string) $endsAt)->startOfDay();

            return max(1, $start->diffInDays($end) + 1);
        } catch (\Throwable) {
            return 30;
        }
    }

    private function baseDurationDays(mixed $templateDurationDays, mixed $templateDurationMonths): int
    {
        if (is_numeric($templateDurationDays) && (int) $templateDurationDays > 0) {
            return (int) $templateDurationDays;
        }

        if (is_numeric($templateDurationMonths) && (int) $templateDurationMonths > 0) {
            return (int) $templateDurationMonths * 30;
        }

        return 30;
    }

    private function resolveChargedAt(mixed $startsAt): Carbon
    {
        try {
            return Carbon::parse((string) $startsAt)->startOfDay();
        } catch (\Throwable) {
            return now()->startOfDay();
        }
    }
};
