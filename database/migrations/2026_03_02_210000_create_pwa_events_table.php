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
        Schema::create('pwa_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('gym_id')->nullable()->constrained('gyms')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('context_gym_slug', 120)->nullable()->index();
            $table->string('event_name', 64)->index();
            $table->string('event_source', 32)->default('web');
            $table->string('mode', 24)->nullable()->index();
            $table->string('user_agent', 255)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index(['gym_id', 'event_name']);
            $table->index(['user_id', 'event_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pwa_events');
    }
};

