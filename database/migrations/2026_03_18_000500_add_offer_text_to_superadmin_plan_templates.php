<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('superadmin_plan_templates')) {
            return;
        }

        Schema::table('superadmin_plan_templates', function (Blueprint $table): void {
            if (! Schema::hasColumn('superadmin_plan_templates', 'offer_text')) {
                $table->string('offer_text', 255)->nullable()->after('discount_price');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('superadmin_plan_templates') || ! Schema::hasColumn('superadmin_plan_templates', 'offer_text')) {
            return;
        }

        Schema::table('superadmin_plan_templates', function (Blueprint $table): void {
            $table->dropColumn('offer_text');
        });
    }
};
