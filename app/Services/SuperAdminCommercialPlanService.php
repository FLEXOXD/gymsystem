<?php

namespace App\Services;

use App\Models\SuperAdminPlanTemplate;
use App\Support\SuperAdminPlanCatalog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SuperAdminCommercialPlanService
{
    /**
     * @return Collection<int, SuperAdminPlanTemplate>
     */
    public function basePlans(): Collection
    {
        if (! $this->supportsCommercialCatalog()) {
            return collect();
        }

        SuperAdminPlanTemplate::ensureDefaultCatalog();

        return SuperAdminPlanTemplate::query()
            ->with(['assignedPlanTemplate' => function ($query): void {
                $query->select([
                    'id',
                    'plan_key',
                    'feature_plan_key',
                    'assigned_plan_template_id',
                    'name',
                    'duration_days',
                    'duration_unit',
                    'duration_months',
                    'price',
                    'discount_price',
                    'offer_text',
                    'status',
                ]);
            }])
            ->whereIn('plan_key', SuperAdminPlanCatalog::keys())
            ->orderByRaw(SuperAdminPlanCatalog::orderCaseSql('plan_key'))
            ->get([
                'id',
                'plan_key',
                'feature_plan_key',
                'assigned_plan_template_id',
                'name',
                'duration_days',
                'duration_unit',
                'duration_months',
                'price',
                'discount_price',
                'offer_text',
                'status',
            ]);
    }

    /**
     * @return Collection<int, SuperAdminPlanTemplate>
     */
    public function commercialPlans(): Collection
    {
        if (! $this->supportsCommercialCatalog()) {
            return collect();
        }

        return SuperAdminPlanTemplate::query()
            ->with('assignedBasePlans:id,assigned_plan_template_id,plan_key,name')
            ->where(function ($query): void {
                $query
                    ->whereNull('plan_key')
                    ->orWhereNotIn('plan_key', SuperAdminPlanCatalog::keys());
            })
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->orderByDesc('id')
            ->get([
                'id',
                'plan_key',
                'feature_plan_key',
                'assigned_plan_template_id',
                'name',
                'duration_days',
                'duration_unit',
                'duration_months',
                'price',
                'discount_price',
                'offer_text',
                'status',
            ]);
    }

    /**
     * @return Collection<int, SuperAdminPlanTemplate>
     */
    public function publicSelections(): Collection
    {
        return $this->basePlans()
            ->map(function (SuperAdminPlanTemplate $basePlan): SuperAdminPlanTemplate {
                return $this->decoratePublicSelection($basePlan);
            })
            ->values();
    }

    public function decoratePublicSelection(SuperAdminPlanTemplate $basePlan): SuperAdminPlanTemplate
    {
        $resolvedTemplate = $basePlan->resolvedPublicTemplate();
        $selection = $resolvedTemplate->is($basePlan) ? $basePlan : clone $resolvedTemplate;

        $slotPlanKey = (string) ($basePlan->plan_key ?? '');

        $selection->setAttribute('plan_key', $slotPlanKey);
        $selection->setAttribute('slot_plan_key', $slotPlanKey);
        $selection->setAttribute('slot_base_template_id', (int) $basePlan->id);
        $selection->setAttribute('slot_base_name', (string) $basePlan->name);
        $selection->setAttribute('slot_assigned_template_id', $resolvedTemplate->is($basePlan) ? null : (int) $resolvedTemplate->id);
        $selection->setAttribute('feature_plan_key', $slotPlanKey !== '' ? $slotPlanKey : $resolvedTemplate->resolvedFeaturePlanKey());
        $selection->setRelation('slotBasePlan', $basePlan);

        return $selection;
    }

    public function supportsCommercialCatalog(): bool
    {
        return Schema::hasTable('superadmin_plan_templates')
            && Schema::hasColumns('superadmin_plan_templates', [
                'id',
                'plan_key',
                'feature_plan_key',
                'assigned_plan_template_id',
                'name',
                'duration_days',
                'duration_unit',
                'duration_months',
                'price',
                'discount_price',
                'offer_text',
                'status',
            ]);
    }
}
