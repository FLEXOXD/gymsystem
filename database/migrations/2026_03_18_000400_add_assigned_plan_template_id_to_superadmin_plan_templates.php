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
            if (! Schema::hasColumn('superadmin_plan_templates', 'assigned_plan_template_id')) {
                $table->foreignId('assigned_plan_template_id')
                    ->nullable()
                    ->after('feature_plan_key')
                    ->constrained('superadmin_plan_templates')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('superadmin_plan_templates') || ! Schema::hasColumn('superadmin_plan_templates', 'assigned_plan_template_id')) {
            return;
        }

        Schema::table('superadmin_plan_templates', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('assigned_plan_template_id');
        });
    }
};
