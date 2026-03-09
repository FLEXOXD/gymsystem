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
        Schema::create('membership_adjustments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('membership_id')->constrained('memberships')->cascadeOnDelete();
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->string('type', 40);
            $table->string('reason', 60);
            $table->text('notes')->nullable();
            $table->date('previous_starts_at')->nullable();
            $table->date('previous_ends_at')->nullable();
            $table->string('previous_status', 20)->nullable();
            $table->date('new_starts_at')->nullable();
            $table->date('new_ends_at')->nullable();
            $table->string('new_status', 20)->nullable();
            $table->integer('days_delta')->default(0);
            $table->timestamps();

            $table->index(['gym_id', 'client_id', 'created_at'], 'membership_adjustments_gym_client_created_idx');
            $table->index(['membership_id', 'created_at'], 'membership_adjustments_membership_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_adjustments');
    }
};
