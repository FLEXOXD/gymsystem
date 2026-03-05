<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_fitness_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->unsignedTinyInteger('age');
            $table->string('sex', 20);
            $table->decimal('height_cm', 6, 2)->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->string('goal', 32);
            $table->string('experience_level', 20);
            $table->unsignedTinyInteger('days_per_week');
            $table->unsignedSmallInteger('session_minutes');
            $table->json('limitations')->nullable();
            $table->json('body_metrics')->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->timestamps();

            $table->unique('client_id');
            $table->index(['gym_id', 'goal']);
            $table->index(['gym_id', 'experience_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_fitness_profiles');
    }
};

