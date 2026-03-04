<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presence_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('check_in_attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->foreignId('check_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('check_in_method', 32)->default('document');
            $table->timestamp('check_in_at');
            $table->foreignId('check_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('check_out_method', 32)->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->string('check_out_reason', 40)->nullable();
            $table->timestamps();

            $table->index(['gym_id', 'check_out_at'], 'presence_sessions_gym_open_idx');
            $table->index(['gym_id', 'client_id', 'check_out_at'], 'presence_sessions_gym_client_open_idx');
            $table->index(['gym_id', 'check_in_at'], 'presence_sessions_gym_checkin_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presence_sessions');
    }
};
