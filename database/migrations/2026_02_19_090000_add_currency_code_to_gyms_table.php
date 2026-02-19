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
            $table->string('currency_code', 3)->default('USD')->after('timezone');
        });

        DB::table('gyms')
            ->whereNull('currency_code')
            ->orWhere('currency_code', '')
            ->update(['currency_code' => 'USD']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table): void {
            $table->dropColumn('currency_code');
        });
    }
};
