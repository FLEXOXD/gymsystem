<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_chat_conversations', function (Blueprint $table): void {
            $table->id();
            $table->string('channel', 30);
            $table->string('requester_type', 30)->default('visitor');
            $table->foreignId('gym_id')->nullable()->constrained('gyms')->nullOnDelete();
            $table->foreignId('initiated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visitor_name', 120)->nullable();
            $table->string('visitor_email', 150)->nullable();
            $table->string('visitor_gym_name', 150)->nullable();
            $table->string('subject', 160)->nullable();
            $table->string('status', 30)->default('bot');
            $table->timestamp('representative_requested_at')->nullable();
            $table->timestamp('representative_joined_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['channel', 'status', 'last_message_at'], 'support_chat_conv_channel_status_last_idx');
            $table->index(['gym_id', 'status', 'last_message_at'], 'support_chat_conv_gym_status_last_idx');
            $table->index(['initiated_by_user_id', 'last_message_at'], 'support_chat_conv_user_last_idx');
        });

        Schema::create('support_chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')
                ->constrained('support_chat_conversations')
                ->cascadeOnDelete();
            $table->string('sender_type', 30);
            $table->foreignId('sender_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sender_label', 120)->nullable();
            $table->text('message');
            $table->string('message_type', 30)->default('text');
            $table->timestamp('read_by_superadmin_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at'], 'support_chat_msg_conversation_created_idx');
            $table->index(['sender_type', 'read_by_superadmin_at'], 'support_chat_msg_sender_read_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_chat_messages');
        Schema::dropIfExists('support_chat_conversations');
    }
};

