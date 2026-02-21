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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone_country_iso', 2)->nullable()->after('email');
            $table->string('phone_country_dial', 8)->nullable()->after('phone_country_iso');
            $table->string('phone_number', 20)->nullable()->after('phone_country_dial');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'phone_country_iso',
                'phone_country_dial',
                'phone_number',
                'last_login_at',
            ]);
        });
    }
};
