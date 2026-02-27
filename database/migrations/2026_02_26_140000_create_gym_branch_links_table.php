<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_branch_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hub_gym_id')
                ->constrained('gyms')
                ->cascadeOnDelete();
            $table->foreignId('branch_gym_id')
                ->constrained('gyms')
                ->cascadeOnDelete();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['hub_gym_id', 'branch_gym_id'], 'gym_branch_links_unique_pair');
            $table->index(['branch_gym_id'], 'gym_branch_links_branch_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_branch_links');
    }
};

