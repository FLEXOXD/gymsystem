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
        Schema::create('push_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gym_id')->nullable()->constrained('gyms')->nullOnDelete();
            $table->string('audience', 20)->default('owners'); // owners|staff|all_users
            $table->string('title', 120);
            $table->string('body', 255);
            $table->string('url', 500)->nullable();
            $table->string('tag', 120)->nullable();
            $table->string('status', 20)->default('queued'); // queued|sending|sent|partial|failed|skipped
            $table->unsignedInteger('total_targets')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->text('last_error')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at'], 'push_campaigns_status_created_idx');
            $table->index(['gym_id', 'created_at'], 'push_campaigns_gym_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_campaigns');
    }
};

