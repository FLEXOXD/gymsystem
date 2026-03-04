<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->string('app_username', 80)->nullable()->after('document_number');
            $table->string('app_password')->nullable()->after('app_username');
            $table->unique(['gym_id', 'app_username']);
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->dropUnique('clients_gym_id_app_username_unique');
            $table->dropColumn(['app_username', 'app_password']);
        });
    }
};