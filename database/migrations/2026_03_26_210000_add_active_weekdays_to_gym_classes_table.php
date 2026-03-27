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
        if (! Schema::hasTable('gym_classes') || Schema::hasColumn('gym_classes', 'active_weekdays')) {
            return;
        }

        Schema::table('gym_classes', function (Blueprint $table) {
            $table->text('active_weekdays')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('gym_classes') || ! Schema::hasColumn('gym_classes', 'active_weekdays')) {
            return;
        }

        Schema::table('gym_classes', function (Blueprint $table) {
            $table->dropColumn('active_weekdays');
        });
    }
};
