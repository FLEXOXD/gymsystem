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
        $hasPlanTemplates = Schema::hasTable('superadmin_plan_templates');

        Schema::table('subscriptions', function (Blueprint $table) use ($hasPlanTemplates): void {
            if (! Schema::hasColumn('subscriptions', 'plan_key')) {
                $table->string('plan_key', 40)->nullable()->after('plan_name');
            }

            if (! Schema::hasColumn('subscriptions', 'plan_template_id')) {
                if ($hasPlanTemplates) {
                    $table->foreignId('plan_template_id')
                        ->nullable()
                        ->after('plan_key')
                        ->constrained('superadmin_plan_templates')
                        ->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('plan_template_id')->nullable()->after('plan_key');
                }
            }

            if (! Schema::hasColumn('subscriptions', 'feature_version')) {
                $table->string('feature_version', 20)->default('v1')->after('plan_template_id');
            }
        });

        $templateByName = [];
        $templateIdByKey = [];

        if ($hasPlanTemplates && Schema::hasColumns('superadmin_plan_templates', ['id', 'name', 'plan_key'])) {
            $templates = DB::table('superadmin_plan_templates')
                ->select(['id', 'name', 'plan_key'])
                ->whereNotNull('plan_key')
                ->get();

            foreach ($templates as $template) {
                $nameKey = $this->normalizeText((string) ($template->name ?? ''));
                $planKey = strtolower(trim((string) ($template->plan_key ?? '')));

                if ($nameKey !== '' && ! array_key_exists($nameKey, $templateByName)) {
                    $templateByName[$nameKey] = (int) $template->id;
                }
                if ($planKey !== '' && ! array_key_exists($planKey, $templateIdByKey)) {
                    $templateIdByKey[$planKey] = (int) $template->id;
                }
            }
        }

        DB::table('subscriptions')
            ->select(['id', 'plan_name', 'price', 'plan_key', 'plan_template_id', 'feature_version'])
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($templateByName, $templateIdByKey): void {
                foreach ($rows as $row) {
                    $planName = (string) ($row->plan_name ?? '');
                    $price = isset($row->price) ? (float) $row->price : null;
                    $resolvedPlanKey = $this->inferPlanKey(
                        rawPlanKey: (string) ($row->plan_key ?? ''),
                        planName: $planName,
                        price: $price
                    );

                    $resolvedTemplateId = $row->plan_template_id !== null ? (int) $row->plan_template_id : null;
                    if ($resolvedTemplateId === null) {
                        $nameKey = $this->normalizeText($planName);
                        if ($nameKey !== '' && array_key_exists($nameKey, $templateByName)) {
                            $resolvedTemplateId = (int) $templateByName[$nameKey];
                        } elseif (array_key_exists($resolvedPlanKey, $templateIdByKey)) {
                            $resolvedTemplateId = (int) $templateIdByKey[$resolvedPlanKey];
                        }
                    }

                    $featureVersion = trim((string) ($row->feature_version ?? ''));
                    if ($featureVersion === '') {
                        $featureVersion = 'v1';
                    }

                    $currentPlanKey = strtolower(trim((string) ($row->plan_key ?? '')));
                    $currentTemplateId = $row->plan_template_id !== null ? (int) $row->plan_template_id : null;
                    $currentFeatureVersion = (string) ($row->feature_version ?? '');

                    $payload = [];
                    if ($currentPlanKey !== $resolvedPlanKey) {
                        $payload['plan_key'] = $resolvedPlanKey;
                    }
                    if ($currentTemplateId !== $resolvedTemplateId) {
                        $payload['plan_template_id'] = $resolvedTemplateId;
                    }
                    if ($currentFeatureVersion !== $featureVersion) {
                        $payload['feature_version'] = $featureVersion;
                    }

                    if ($payload !== []) {
                        DB::table('subscriptions')
                            ->where('id', (int) $row->id)
                            ->update($payload);
                    }
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('subscriptions', 'feature_version')) {
            Schema::table('subscriptions', function (Blueprint $table): void {
                $table->dropColumn('feature_version');
            });
        }

        if (Schema::hasColumn('subscriptions', 'plan_template_id')) {
            if (Schema::hasTable('superadmin_plan_templates')) {
                try {
                    Schema::table('subscriptions', function (Blueprint $table): void {
                        $table->dropForeign(['plan_template_id']);
                    });
                } catch (\Throwable) {
                    // Column may exist without FK in non-standard environments.
                }
            }

            Schema::table('subscriptions', function (Blueprint $table): void {
                $table->dropColumn('plan_template_id');
            });
        }

        if (Schema::hasColumn('subscriptions', 'plan_key')) {
            Schema::table('subscriptions', function (Blueprint $table): void {
                $table->dropColumn('plan_key');
            });
        }
    }

    private function inferPlanKey(string $rawPlanKey, string $planName, ?float $price): string
    {
        $knownKeys = SuperAdminPlanCatalog::keys();
        $normalizedKey = strtolower(trim($rawPlanKey));

        if (in_array($normalizedKey, $knownKeys, true)) {
            return $normalizedKey;
        }

        $normalizedName = $this->normalizeText($planName);
        $nameMatchers = [
            'basico' => 'basico',
            'profesional' => 'profesional',
            'premium' => 'premium',
            'sucursales' => 'sucursales',
            'sucursal' => 'sucursales',
            'multi sede' => 'sucursales',
            'multi-sede' => 'sucursales',
            'multi gym' => 'sucursales',
            'multi-gym' => 'sucursales',
        ];

        foreach ($nameMatchers as $needle => $planKey) {
            if ($normalizedName !== '' && str_contains($normalizedName, $needle)) {
                return $planKey;
            }
        }

        if ($price !== null) {
            foreach (SuperAdminPlanCatalog::defaults() as $default) {
                $catalogPlanKey = strtolower(trim((string) ($default['plan_key'] ?? '')));
                if (! in_array($catalogPlanKey, $knownKeys, true)) {
                    continue;
                }

                $referencePrices = [(float) ($default['price'] ?? 0)];
                if (array_key_exists('discount_price', $default)) {
                    $referencePrices[] = (float) ($default['discount_price'] ?? 0);
                }

                foreach ($referencePrices as $referencePrice) {
                    if (abs($referencePrice - $price) < 0.01) {
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

