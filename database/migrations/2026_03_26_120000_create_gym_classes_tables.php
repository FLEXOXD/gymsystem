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
        Schema::create('gym_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 120);
            $table->string('category', 80)->nullable();
            $table->string('level', 40)->nullable();
            $table->string('instructor_name', 120)->nullable();
            $table->string('room_name', 80)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->unsignedInteger('capacity')->default(1);
            $table->boolean('allow_waitlist')->default(true);
            $table->string('status', 20)->default('scheduled');
            $table->timestamps();

            $table->index(['gym_id', 'status', 'starts_at']);
            $table->index(['gym_id', 'starts_at']);
        });

        Schema::create('gym_class_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('gym_class_id')->constrained('gym_classes')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('status', 20)->default('reserved');
            $table->dateTime('reserved_at')->nullable();
            $table->dateTime('waitlisted_at')->nullable();
            $table->dateTime('promoted_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('attended_at')->nullable();
            $table->dateTime('day_reminder_sent_at')->nullable();
            $table->dateTime('hour_reminder_sent_at')->nullable();
            $table->timestamps();

            $table->unique(['gym_class_id', 'client_id']);
            $table->index(['gym_class_id', 'status']);
            $table->index(['gym_id', 'client_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_class_reservations');
        Schema::dropIfExists('gym_classes');
    }
};
