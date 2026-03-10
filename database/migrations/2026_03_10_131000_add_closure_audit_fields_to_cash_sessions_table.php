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
        Schema::table('cash_sessions', function (Blueprint $table): void {
            if (! Schema::hasColumn('cash_sessions', 'closing_notes')) {
                $table->text('closing_notes')->nullable();
            }

            if (! Schema::hasColumn('cash_sessions', 'difference_reason')) {
                $table->text('difference_reason')->nullable();
            }

            if (! Schema::hasColumn('cash_sessions', 'close_source')) {
                $table->string('close_source', 30)->default('manual');
            }
        });

        DB::table('cash_sessions')
            ->whereNull('close_source')
            ->update(['close_source' => 'manual']);

        DB::table('cash_sessions')
            ->where('status', 'closed')
            ->whereNotNull('notes')
            ->whereNull('closing_notes')
            ->update([
                'closing_notes' => DB::raw('notes'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table): void {
            if (Schema::hasColumn('cash_sessions', 'close_source')) {
                $table->dropColumn('close_source');
            }

            if (Schema::hasColumn('cash_sessions', 'difference_reason')) {
                $table->dropColumn('difference_reason');
            }

            if (Schema::hasColumn('cash_sessions', 'closing_notes')) {
                $table->dropColumn('closing_notes');
            }
        });
    }
};
