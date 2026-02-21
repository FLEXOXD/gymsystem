<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('superadmin_promotion_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('plan_template_id')
                ->nullable()
                ->constrained('superadmin_plan_templates')
                ->nullOnDelete();
            $table->string('name', 120);
            $table->string('description', 255)->nullable();
            $table->string('type', 40)->default('percentage');
            $table->decimal('value', 10, 2)->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedInteger('max_uses')->nullable();
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at'], 'sap_templates_status_dates_idx');
            $table->index(['plan_template_id'], 'sap_templates_plan_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('superadmin_promotion_templates');
    }
};

