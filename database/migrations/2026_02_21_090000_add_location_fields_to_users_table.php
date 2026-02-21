<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'address_state')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('address_state', 120)->nullable()->after('country_name');
            });
        }

        if (! Schema::hasColumn('users', 'address_city')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('address_city', 120)->nullable()->after('address_state');
            });
        }

        if (! Schema::hasColumn('users', 'address_line')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('address_line', 180)->nullable()->after('address_city');
            });
        }

        // Backfill from gym location for existing gym users.
        DB::table('users')
            ->join('gyms', 'gyms.id', '=', 'users.gym_id')
            ->select([
                'users.id as user_id',
                'users.address_state as user_state',
                'users.address_city as user_city',
                'users.address_line as user_line',
                'gyms.address_state as gym_state',
                'gyms.address_city as gym_city',
                'gyms.address_line as gym_line',
            ])
            ->orderBy('users.id')
            ->chunk(200, function ($rows): void {
                foreach ($rows as $row) {
                    $updates = [];
                    if (trim((string) ($row->user_state ?? '')) === '' && trim((string) ($row->gym_state ?? '')) !== '') {
                        $updates['address_state'] = (string) $row->gym_state;
                    }
                    if (trim((string) ($row->user_city ?? '')) === '' && trim((string) ($row->gym_city ?? '')) !== '') {
                        $updates['address_city'] = (string) $row->gym_city;
                    }
                    if (trim((string) ($row->user_line ?? '')) === '' && trim((string) ($row->gym_line ?? '')) !== '') {
                        $updates['address_line'] = (string) $row->gym_line;
                    }
                    if ($updates !== []) {
                        DB::table('users')->where('id', (int) $row->user_id)->update($updates);
                    }
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $columns = ['address_state', 'address_city', 'address_line'];
        foreach ($columns as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
            }
        }
    }
};

