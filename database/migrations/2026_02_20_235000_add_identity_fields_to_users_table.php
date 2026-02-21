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
            if (! Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 24)->nullable()->after('country_name');
            }

            if (! Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('gender');
            }

            if (! Schema::hasColumn('users', 'identification_type')) {
                $table->string('identification_type', 24)->nullable()->after('birth_date');
            }

            if (! Schema::hasColumn('users', 'identification_number')) {
                $table->string('identification_number', 40)->nullable()->after('identification_type');
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
            if (Schema::hasColumn('users', 'identification_number')) {
                $table->dropColumn('identification_number');
            }

            if (Schema::hasColumn('users', 'identification_type')) {
                $table->dropColumn('identification_type');
            }

            if (Schema::hasColumn('users', 'birth_date')) {
                $table->dropColumn('birth_date');
            }

            if (Schema::hasColumn('users', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};

