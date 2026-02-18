<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE clients MODIFY status VARCHAR(20) NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE client_credentials MODIFY status VARCHAR(20) NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE plans MODIFY status VARCHAR(20) NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE clients MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE client_credentials MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE plans MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
    }
};
