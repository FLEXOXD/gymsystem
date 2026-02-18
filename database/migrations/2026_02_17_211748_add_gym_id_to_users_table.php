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
        if (! Schema::hasColumn('users', 'gym_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('gym_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('gyms')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'gym_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('gym_id');
            });
        }
    }
};
