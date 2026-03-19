<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('superadmin_promotion_templates')) {
            return;
        }

        Schema::table('superadmin_promotion_templates', function (Blueprint $table): void {
            if (! Schema::hasColumn('superadmin_promotion_templates', 'duration_unit')) {
                $table->string('duration_unit', 20)->default('months')->after('duration_months');
            }
            if (! Schema::hasColumn('superadmin_promotion_templates', 'duration_days')) {
                $table->unsignedInteger('duration_days')->nullable()->after('duration_unit');
            }
        });

        if (
            Schema::hasColumns('superadmin_promotion_templates', ['duration_months', 'duration_unit'])
            && Schema::hasColumn('superadmin_promotion_templates', 'duration_days')
        ) {
            DB::table('superadmin_promotion_templates')
                ->whereNull('duration_unit')
                ->orWhere('duration_unit', '')
                ->update([
                    'duration_unit' => 'months',
                    'duration_days' => null,
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('superadmin_promotion_templates')) {
            return;
        }

        Schema::table('superadmin_promotion_templates', function (Blueprint $table): void {
            if (Schema::hasColumn('superadmin_promotion_templates', 'duration_days')) {
                $table->dropColumn('duration_days');
            }
            if (Schema::hasColumn('superadmin_promotion_templates', 'duration_unit')) {
                $table->dropColumn('duration_unit');
            }
        });
    }
};
