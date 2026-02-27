<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('superadmin_plan_templates', function (Blueprint $table): void {
            if (! Schema::hasColumn('superadmin_plan_templates', 'plan_key')) {
                $table->string('plan_key', 40)->nullable()->after('id');
            }
            if (! Schema::hasColumn('superadmin_plan_templates', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            }
            $table->unique('plan_key', 'superadmin_plan_templates_plan_key_unique');
        });

        $defaults = [
            [
                'plan_key' => 'basico',
                'name' => 'Plan basico',
                'duration_unit' => 'days',
                'duration_days' => 30,
                'duration_months' => 1,
                'price' => 25.00,
                'discount_price' => 19.00,
                'status' => 'active',
            ],
            [
                'plan_key' => 'profesional',
                'name' => 'Plan profesional',
                'duration_unit' => 'days',
                'duration_days' => 30,
                'duration_months' => 1,
                'price' => 35.00,
                'discount_price' => 25.00,
                'status' => 'active',
            ],
            [
                'plan_key' => 'premium',
                'name' => 'Plan premium',
                'duration_unit' => 'days',
                'duration_days' => 30,
                'duration_months' => 1,
                'price' => 50.00,
                'discount_price' => 35.00,
                'status' => 'active',
            ],
            [
                'plan_key' => 'sucursales',
                'name' => 'Plan sucursales',
                'duration_unit' => 'days',
                'duration_days' => 30,
                'duration_months' => 1,
                'price' => 90.00,
                'discount_price' => 45.00,
                'status' => 'active',
            ],
        ];
        $rows = DB::table('superadmin_plan_templates')
            ->select(['id', 'name', 'plan_key', 'price', 'discount_price', 'status'])
            ->orderBy('id')
            ->get();

        $usedIds = [];
        $now = now();

        foreach ($defaults as $default) {
            $planKey = (string) $default['plan_key'];
            $planName = (string) $default['name'];

            $row = $rows->firstWhere('plan_key', $planKey);
            if (! $row) {
                $row = $rows->first(function (object $item) use ($planName, $usedIds): bool {
                    if (in_array((int) $item->id, $usedIds, true)) {
                        return false;
                    }

                    return strtolower(trim((string) $item->name)) === strtolower($planName);
                });
            }

            if ($row) {
                DB::table('superadmin_plan_templates')
                    ->where('id', (int) $row->id)
                    ->update([
                        'plan_key' => $planKey,
                        'name' => $planName,
                        'duration_unit' => (string) $default['duration_unit'],
                        'duration_days' => (int) $default['duration_days'],
                        'duration_months' => (int) $default['duration_months'],
                        'discount_price' => $row->discount_price !== null ? (float) $row->discount_price : (float) $default['discount_price'],
                        'updated_at' => $now,
                    ]);
                $usedIds[] = (int) $row->id;
                continue;
            }

            DB::table('superadmin_plan_templates')->insert([
                'plan_key' => $planKey,
                'name' => $planName,
                'duration_unit' => (string) $default['duration_unit'],
                'duration_days' => (int) $default['duration_days'],
                'duration_months' => (int) $default['duration_months'],
                'price' => (float) $default['price'],
                'discount_price' => (float) $default['discount_price'],
                'status' => (string) $default['status'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::table('superadmin_promotion_templates', function (Blueprint $table): void {
            if (! Schema::hasColumn('superadmin_promotion_templates', 'duration_months')) {
                $table->unsignedInteger('duration_months')->nullable()->after('max_uses');
            }
        });
    }

    public function down(): void
    {
        Schema::table('superadmin_promotion_templates', function (Blueprint $table): void {
            if (Schema::hasColumn('superadmin_promotion_templates', 'duration_months')) {
                $table->dropColumn('duration_months');
            }
        });

        Schema::table('superadmin_plan_templates', function (Blueprint $table): void {
            $table->dropUnique('superadmin_plan_templates_plan_key_unique');
            if (Schema::hasColumn('superadmin_plan_templates', 'discount_price')) {
                $table->dropColumn('discount_price');
            }
            if (Schema::hasColumn('superadmin_plan_templates', 'plan_key')) {
                $table->dropColumn('plan_key');
            }
        });
    }
};
