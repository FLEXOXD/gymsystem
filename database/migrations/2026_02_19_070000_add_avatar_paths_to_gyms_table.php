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
        Schema::table('gyms', function (Blueprint $table) {
            $table->string('avatar_male_path')->nullable()->after('logo_path');
            $table->string('avatar_female_path')->nullable()->after('avatar_male_path');
            $table->string('avatar_neutral_path')->nullable()->after('avatar_female_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_male_path',
                'avatar_female_path',
                'avatar_neutral_path',
            ]);
        });
    }
};
