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
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            if (! Schema::hasColumn('users', 'can_open_cash')) {
                $table->boolean('can_open_cash')->default(false)->after('is_active');
            }
            if (! Schema::hasColumn('users', 'can_close_cash')) {
                $table->boolean('can_close_cash')->default(false)->after('can_open_cash');
            }
            if (! Schema::hasColumn('users', 'can_manage_cash_movements')) {
                $table->boolean('can_manage_cash_movements')->default(true)->after('can_close_cash');
            }
        });

        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')
                ->where('role', 'cashier')
                ->update([
                    'is_active' => true,
                    'can_open_cash' => false,
                    'can_close_cash' => false,
                    'can_manage_cash_movements' => true,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'can_manage_cash_movements')) {
                $table->dropColumn('can_manage_cash_movements');
            }
            if (Schema::hasColumn('users', 'can_close_cash')) {
                $table->dropColumn('can_close_cash');
            }
            if (Schema::hasColumn('users', 'can_open_cash')) {
                $table->dropColumn('can_open_cash');
            }
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};

