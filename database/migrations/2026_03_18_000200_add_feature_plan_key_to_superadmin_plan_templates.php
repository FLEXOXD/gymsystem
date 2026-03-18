<?php

use App\Support\SuperAdminPlanCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('superadmin_plan_templates', function (Blueprint $table): void {
            if (! Schema::hasColumn('superadmin_plan_templates', 'feature_plan_key')) {
                $table->string('feature_plan_key', 40)->nullable()->after('plan_key');
            }
        });

        if (! Schema::hasColumns('superadmin_plan_templates', ['id', 'plan_key', 'feature_plan_key', 'name', 'price'])) {
            return;
        }

        DB::table('superadmin_plan_templates')
            ->select(['id', 'plan_key', 'feature_plan_key', 'name', 'price', 'discount_price'])
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $currentFeaturePlanKey = strtolower(trim((string) ($row->feature_plan_key ?? '')));
                    if (in_array($currentFeaturePlanKey, SuperAdminPlanCatalog::keys(), true)) {
                        continue;
                    }

                    $resolvedFeaturePlanKey = $this->inferFeaturePlanKey(
                        rawPlanKey: (string) ($row->plan_key ?? ''),
                        planName: (string) ($row->name ?? ''),
                        price: isset($row->price) ? (float) $row->price : null,
                        discountPrice: isset($row->discount_price) ? (float) $row->discount_price : null,
                    );

                    DB::table('superadmin_plan_templates')
                        ->where('id', (int) $row->id)
                        ->update([
                            'feature_plan_key' => $resolvedFeaturePlanKey,
                        ]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('superadmin_plan_templates', 'feature_plan_key')) {
            return;
        }

        Schema::table('superadmin_plan_templates', function (Blueprint $table): void {
            $table->dropColumn('feature_plan_key');
        });
    }

    private function inferFeaturePlanKey(string $rawPlanKey, string $planName, ?float $price, ?float $discountPrice): string
    {
        $knownKeys = SuperAdminPlanCatalog::keys();
        $normalizedKey = strtolower(trim($rawPlanKey));
        if (in_array($normalizedKey, $knownKeys, true)) {
            return $normalizedKey;
        }

        $normalizedName = $this->normalizeText($planName);
        $nameMatchers = [
            'control' => 'basico',
            'basico' => 'basico',
            'crecimiento' => 'profesional',
            'profesional' => 'profesional',
            'elite' => 'premium',
            'premium' => 'premium',
            'sucursales' => 'sucursales',
            'sucursal' => 'sucursales',
            'multi sede' => 'sucursales',
            'multi-sede' => 'sucursales',
            'multi gym' => 'sucursales',
            'multi-gym' => 'sucursales',
        ];

        foreach ($nameMatchers as $needle => $featurePlanKey) {
            if ($normalizedName !== '' && str_contains($normalizedName, $needle)) {
                return $featurePlanKey;
            }
        }

        $referencePrices = [];
        if ($price !== null) {
            $referencePrices[] = $price;
        }
        if ($discountPrice !== null) {
            $referencePrices[] = $discountPrice;
        }

        foreach ($referencePrices as $referencePrice) {
            foreach (SuperAdminPlanCatalog::defaults() as $default) {
                $catalogPlanKey = strtolower(trim((string) ($default['plan_key'] ?? '')));
                if (! in_array($catalogPlanKey, $knownKeys, true)) {
                    continue;
                }

                $catalogPrices = [(float) ($default['price'] ?? 0)];
                if (array_key_exists('discount_price', $default)) {
                    $catalogPrices[] = (float) ($default['discount_price'] ?? 0);
                }

                foreach ($catalogPrices as $catalogPrice) {
                    if (abs($catalogPrice - $referencePrice) < 0.01) {
                        return $catalogPlanKey;
                    }
                }
            }
        }

        return 'basico';
    }

    private function normalizeText(string $value): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($value));

        return mb_strtolower((string) ($normalized ?? ''));
    }
};
