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
        Schema::create('attendance_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('attendances_count')->default(0);
            $table->timestamps();

            $table->unique(['gym_id', 'date'], 'attendance_daily_summaries_gym_date_unique');
            $table->index(['gym_id', 'date'], 'attendance_daily_summaries_gym_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_daily_summaries');
    }
};
