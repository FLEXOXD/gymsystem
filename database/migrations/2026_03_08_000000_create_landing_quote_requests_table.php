<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_quote_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('first_name', 80);
            $table->string('last_name', 80);
            $table->string('email', 150);
            $table->string('phone_country_code', 10);
            $table->string('phone_number', 30);
            $table->string('country', 120);
            $table->unsignedSmallInteger('professionals_count');
            $table->string('requested_plan', 60)->nullable();
            $table->string('source', 60)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->foreignId('read_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['read_at', 'created_at']);
            $table->index(['email', 'created_at']);
            $table->index(['country', 'created_at']);
            $table->index(['requested_plan', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_quote_requests');
    }
};
