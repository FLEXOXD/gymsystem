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
        Schema::table('push_campaigns', function (Blueprint $table): void {
            if (! Schema::hasColumn('push_campaigns', 'detail_text')) {
                $table->text('detail_text')->nullable()->after('body');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('push_campaigns', function (Blueprint $table): void {
            if (Schema::hasColumn('push_campaigns', 'detail_text')) {
                $table->dropColumn('detail_text');
            }
        });
    }
};

