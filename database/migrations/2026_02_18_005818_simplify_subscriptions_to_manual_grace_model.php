<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('subscriptions', 'last_payment_method')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->string('last_payment_method', 40)->nullable()->after('status');
            });
        }

        if (! Schema::hasColumn('subscriptions', 'grace_days')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->unsignedInteger('grace_days')->default(3)->after('last_payment_method');
            });
        }

        DB::table('subscriptions')
            ->where('status', 'trial')
            ->update(['status' => 'active']);

        DB::table('subscriptions')
            ->whereNotIn('status', ['active', 'grace', 'suspended'])
            ->update(['status' => 'suspended']);

        $gymIds = DB::table('subscriptions')
            ->select('gym_id')
            ->distinct()
            ->pluck('gym_id');

        foreach ($gymIds as $gymId) {
            $keepId = DB::table('subscriptions')
                ->where('gym_id', $gymId)
                ->max('id');

            DB::table('subscriptions')
                ->where('gym_id', $gymId)
                ->where('id', '<>', $keepId)
                ->delete();
        }

        if (Schema::hasColumn('subscriptions', 'trial_ends_at')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('trial_ends_at');
            });
        }

        if (! $this->hasIndex('subscriptions', 'subscriptions_gym_id_unique')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->unique('gym_id');
            });
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE subscriptions MODIFY status VARCHAR(20) NOT NULL DEFAULT 'active'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE subscriptions MODIFY status VARCHAR(20) NOT NULL DEFAULT 'trial'");
        }

        if (! Schema::hasColumn('subscriptions', 'trial_ends_at')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->date('trial_ends_at')->nullable()->after('ends_at');
            });
        }

        if ($this->hasIndex('subscriptions', 'subscriptions_gym_id_unique')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropUnique('subscriptions_gym_id_unique');
            });
        }

        if (Schema::hasColumn('subscriptions', 'last_payment_method')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('last_payment_method');
            });
        }

        if (Schema::hasColumn('subscriptions', 'grace_days')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('grace_days');
            });
        }

        if (! $this->hasIndex('subscriptions', 'subscriptions_gym_id_status_index')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->index(['gym_id', 'status']);
            });
        }
    }
};
