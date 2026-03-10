<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_fitness_profiles', function (Blueprint $table): void {
            $table->string('secondary_goal', 32)->nullable()->after('goal');
            $table->index(['gym_id', 'secondary_goal']);
        });
    }

    public function down(): void
    {
        Schema::table('client_fitness_profiles', function (Blueprint $table): void {
            $table->dropIndex(['gym_id', 'secondary_goal']);
            $table->dropColumn('secondary_goal');
        });
    }
};
