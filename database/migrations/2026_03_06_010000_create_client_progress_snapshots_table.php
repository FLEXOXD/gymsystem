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
        Schema::create('client_progress_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->date('snapshot_for_date');
            $table->date('source_attendance_date')->nullable();
            $table->json('sections');
            $table->timestamps();

            $table->unique(['gym_id', 'client_id'], 'client_progress_snapshots_gym_client_unique');
            $table->index('snapshot_for_date', 'client_progress_snapshots_snapshot_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_progress_snapshots');
    }
};

