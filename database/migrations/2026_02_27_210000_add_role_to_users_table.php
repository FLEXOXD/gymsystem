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
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('role', 30)->default('owner');
                $table->index(['gym_id', 'role'], 'users_gym_role_idx');
            });
        }

        DB::table('users')
            ->whereNull('gym_id')
            ->update(['role' => 'superadmin']);

        DB::table('users')
            ->whereNotNull('gym_id')
            ->where(function ($query): void {
                $query->whereNull('role')
                    ->orWhere('role', '');
            })
            ->update(['role' => 'owner']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_gym_role_idx');
            $table->dropColumn('role');
        });
    }
};

