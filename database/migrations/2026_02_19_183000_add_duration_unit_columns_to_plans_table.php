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
        Schema::table('plans', function (Blueprint $table) {
            if (! Schema::hasColumn('plans', 'duration_unit')) {
                $table->string('duration_unit', 20)->default('days')->after('duration_days');
            }

            if (! Schema::hasColumn('plans', 'duration_months')) {
                $table->unsignedSmallInteger('duration_months')->nullable()->after('duration_unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'duration_months')) {
                $table->dropColumn('duration_months');
            }
            if (Schema::hasColumn('plans', 'duration_unit')) {
                $table->dropColumn('duration_unit');
            }
        });
    }
};
