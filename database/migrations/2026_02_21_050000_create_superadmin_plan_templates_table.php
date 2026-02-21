<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('superadmin_plan_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->unsignedInteger('duration_days');
            $table->string('duration_unit', 20)->default('days');
            $table->unsignedInteger('duration_months')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('superadmin_plan_templates');
    }
};

