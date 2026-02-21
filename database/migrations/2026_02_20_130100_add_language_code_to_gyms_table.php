<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gyms', function (Blueprint $table): void {
            $table->string('language_code', 5)->default('es')->after('currency_code');
        });

        DB::table('gyms')
            ->whereNull('language_code')
            ->update(['language_code' => 'es']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table): void {
            $table->dropColumn('language_code');
        });
    }
};
