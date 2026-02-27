<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_acceptances', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name', 120);
            $table->string('email', 190);
            $table->string('document_key', 40);
            $table->string('document_label', 120);
            $table->string('legal_version', 30);
            $table->boolean('accepted')->default(false);
            $table->timestamp('accepted_at');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['document_key', 'accepted_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_acceptances');
    }
};

