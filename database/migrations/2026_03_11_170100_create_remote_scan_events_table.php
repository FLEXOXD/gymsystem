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
        Schema::create('remote_scan_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remote_scan_session_id')->constrained('remote_scan_sessions')->cascadeOnDelete();
            $table->string('code', 120);
            $table->string('source', 20)->default('camera');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['remote_scan_session_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_scan_events');
    }
};
