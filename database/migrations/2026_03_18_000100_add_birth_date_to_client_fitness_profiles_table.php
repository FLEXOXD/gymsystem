<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_fitness_profiles', function (Blueprint $table): void {
            $table->date('birth_date')->nullable()->after('client_id');
            $table->index(['gym_id', 'birth_date']);
        });
    }

    public function down(): void
    {
        Schema::table('client_fitness_profiles', function (Blueprint $table): void {
            $table->dropIndex(['gym_id', 'birth_date']);
            $table->dropColumn('birth_date');
        });
    }
};

