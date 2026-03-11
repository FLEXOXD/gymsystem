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
        Schema::create('remote_scan_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('context', 20);
            $table->string('channel_token', 80)->unique();
            $table->string('status', 20)->default('open');
            $table->timestamp('expires_at')->index();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['gym_id', 'created_by', 'context']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_scan_sessions');
    }
};
