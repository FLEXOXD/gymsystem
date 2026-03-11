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
        Schema::table('clients', function (Blueprint $table): void {
            $table->foreignId('created_by')
                ->nullable()
                ->after('gym_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('created_by_name_snapshot')->nullable()->after('created_by');
            $table->string('created_by_role_snapshot', 30)->nullable()->after('created_by_name_snapshot');
            $table->foreignId('last_managed_by')
                ->nullable()
                ->after('created_by_role_snapshot')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('last_managed_by_name_snapshot')->nullable()->after('last_managed_by');
            $table->string('last_managed_by_role_snapshot', 30)->nullable()->after('last_managed_by_name_snapshot');
            $table->timestamp('last_managed_at')->nullable()->after('updated_at');

            $table->index(['gym_id', 'created_by'], 'clients_gym_created_by_idx');
            $table->index(['gym_id', 'last_managed_by'], 'clients_gym_last_managed_by_idx');
        });

        DB::table('clients')
            ->whereNull('created_by_name_snapshot')
            ->update([
                'created_by_name_snapshot' => 'Registro previo',
                'created_by_role_snapshot' => 'legacy',
            ]);

        DB::table('clients')
            ->whereNull('last_managed_by_name_snapshot')
            ->update([
                'last_managed_by_name_snapshot' => 'Registro previo',
                'last_managed_by_role_snapshot' => 'legacy',
            ]);

        DB::table('clients')
            ->whereNull('last_managed_at')
            ->update([
                'last_managed_at' => DB::raw('COALESCE(updated_at, created_at)'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->dropIndex('clients_gym_created_by_idx');
            $table->dropIndex('clients_gym_last_managed_by_idx');
            $table->dropColumn([
                'created_by_name_snapshot',
                'created_by_role_snapshot',
                'last_managed_by_name_snapshot',
                'last_managed_by_role_snapshot',
                'last_managed_at',
            ]);
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('last_managed_by');
        });
    }
};
