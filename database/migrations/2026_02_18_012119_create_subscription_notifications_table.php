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
        Schema::create('subscription_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->string('type', 20); // expires_7|expires_3|expires_1|grace_1|grace_2|grace_3
            $table->string('channel', 20)->default('internal'); // internal|email
            $table->string('status', 20)->default('pending'); // pending|sent|skipped
            $table->date('scheduled_for');
            $table->dateTime('sent_at')->nullable();
            $table->text('message_snapshot');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['gym_id', 'scheduled_for', 'status'], 'sub_notifications_gym_date_status_idx');
            $table->index(['subscription_id', 'type'], 'sub_notifications_subscription_type_idx');
            $table->unique(['subscription_id', 'type', 'scheduled_for'], 'sub_notifications_unique_per_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_notifications');
    }
};
