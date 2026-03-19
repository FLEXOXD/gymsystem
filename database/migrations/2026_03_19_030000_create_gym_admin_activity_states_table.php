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
        Schema::create('gym_admin_activity_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete()->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('gym_name', 160)->nullable();
            $table->string('user_name', 160)->nullable();
            $table->string('user_email')->nullable();
            $table->timestamp('last_login_at')->nullable()->index();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->string('last_activity_signal', 40)->nullable();
            $table->string('last_channel', 20)->nullable();
            $table->string('last_route_name', 120)->nullable();
            $table->string('last_path', 255)->nullable();
            $table->string('last_ip_address', 45)->nullable();
            $table->string('last_user_agent', 1024)->nullable();
            $table->boolean('last_via_remember')->default(false);
            $table->timestamps();

            $table->index(['last_activity_at', 'last_channel'], 'gym_admin_activity_states_activity_channel_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_admin_activity_states');
    }
};
