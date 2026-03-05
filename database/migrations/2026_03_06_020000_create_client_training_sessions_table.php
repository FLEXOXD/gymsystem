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
        Schema::create('client_training_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->date('session_date');
            $table->unsignedSmallInteger('planned_minutes')->default(60);
            $table->string('status', 20)->default('active');
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->string('finish_reason', 30)->nullable();
            $table->timestamps();

            $table->unique(['gym_id', 'client_id', 'session_date'], 'client_training_sessions_unique_day');
            $table->index(['gym_id', 'client_id', 'status'], 'client_training_sessions_status_idx');
            $table->index(['gym_id', 'session_date'], 'client_training_sessions_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_training_sessions');
    }
};

