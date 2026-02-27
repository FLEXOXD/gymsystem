<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_branch_links', function (Blueprint $table): void {
            if (! Schema::hasColumn('gym_branch_links', 'branch_plan_key')) {
                $table->string('branch_plan_key', 24)->default('basico')->after('branch_gym_id');
            }

            if (! Schema::hasColumn('gym_branch_links', 'cash_managed_by_hub')) {
                $table->boolean('cash_managed_by_hub')->default(true)->after('branch_plan_key');
            }

            if (! Schema::hasColumn('gym_branch_links', 'status')) {
                $table->string('status', 16)->default('active')->after('cash_managed_by_hub');
            }
        });

        // Keep one ownership row per branch before adding unique branch constraint.
        $duplicates = DB::table('gym_branch_links')
            ->select('branch_gym_id', DB::raw('MIN(id) AS keep_id'), DB::raw('COUNT(*) AS total'))
            ->groupBy('branch_gym_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('gym_branch_links')
                ->where('branch_gym_id', (int) $duplicate->branch_gym_id)
                ->where('id', '<>', (int) $duplicate->keep_id)
                ->delete();
        }

        if (! $this->hasIndex('gym_branch_links', 'gym_branch_links_branch_unique')) {
            Schema::table('gym_branch_links', function (Blueprint $table): void {
                $table->unique('branch_gym_id', 'gym_branch_links_branch_unique');
            });
        }
    }

    public function down(): void
    {
        if ($this->hasIndex('gym_branch_links', 'gym_branch_links_branch_unique')) {
            Schema::table('gym_branch_links', function (Blueprint $table): void {
                $table->dropUnique('gym_branch_links_branch_unique');
            });
        }

        if (! $this->hasIndex('gym_branch_links', 'gym_branch_links_branch_idx')) {
            Schema::table('gym_branch_links', function (Blueprint $table): void {
                $table->index('branch_gym_id', 'gym_branch_links_branch_idx');
            });
        }

        Schema::table('gym_branch_links', function (Blueprint $table): void {
            if (Schema::hasColumn('gym_branch_links', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('gym_branch_links', 'cash_managed_by_hub')) {
                $table->dropColumn('cash_managed_by_hub');
            }

            if (Schema::hasColumn('gym_branch_links', 'branch_plan_key')) {
                $table->dropColumn('branch_plan_key');
            }
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);

            return ! empty($result);
        }

        if ($driver === 'sqlite') {
            $result = DB::select("PRAGMA index_list('{$table}')");
            foreach ($result as $row) {
                if (($row->name ?? null) === $index) {
                    return true;
                }
            }
        }

        return false;
    }
};
