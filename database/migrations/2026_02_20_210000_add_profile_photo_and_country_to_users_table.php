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
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('phone_number');
            }

            if (! Schema::hasColumn('users', 'country_iso')) {
                $table->string('country_iso', 2)->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'country_name')) {
                $table->string('country_name', 80)->nullable()->after('country_iso');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'profile_photo_path')) {
                $table->dropColumn('profile_photo_path');
            }

            if (Schema::hasColumn('users', 'country_iso')) {
                $table->dropColumn('country_iso');
            }

            if (Schema::hasColumn('users', 'country_name')) {
                $table->dropColumn('country_name');
            }
        });
    }
};

