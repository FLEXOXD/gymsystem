<?php

use App\Support\GymLocationCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('gyms', 'address_country_code')) {
            Schema::table('gyms', function (Blueprint $table): void {
                $table->string('address_country_code', 5)->nullable()->after('address');
            });
        }

        if (! Schema::hasColumn('gyms', 'address_country_name')) {
            Schema::table('gyms', function (Blueprint $table): void {
                $table->string('address_country_name', 120)->nullable()->after('address_country_code');
            });
        }

        if (! Schema::hasColumn('gyms', 'address_state')) {
            Schema::table('gyms', function (Blueprint $table): void {
                $table->string('address_state', 120)->nullable()->after('address_country_name');
            });
        }

        if (! Schema::hasColumn('gyms', 'address_city')) {
            Schema::table('gyms', function (Blueprint $table): void {
                $table->string('address_city', 120)->nullable()->after('address_state');
            });
        }

        if (! Schema::hasColumn('gyms', 'address_line')) {
            Schema::table('gyms', function (Blueprint $table): void {
                $table->string('address_line', 180)->nullable()->after('address_city');
            });
        }

        $catalog = GymLocationCatalog::catalog();
        $countryCodeByLabel = [];
        foreach ($catalog as $code => $meta) {
            $label = strtolower(trim((string) ($meta['label'] ?? '')));
            if ($label !== '') {
                $countryCodeByLabel[$label] = (string) $code;
            }
        }

        DB::table('gyms')
            ->select([
                'id',
                'address',
                'address_country_code',
                'address_country_name',
                'address_state',
                'address_city',
                'address_line',
            ])
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($countryCodeByLabel): void {
                foreach ($rows as $row) {
                    $address = trim((string) ($row->address ?? ''));
                    if ($address === '') {
                        continue;
                    }

                    $parts = array_values(array_filter(array_map(
                        static fn ($piece): string => trim((string) $piece),
                        explode(',', $address)
                    ), static fn ($piece): bool => $piece !== ''));

                    if (count($parts) < 3) {
                        continue;
                    }

                    $countryName = $parts[count($parts) - 1];
                    $state = $parts[count($parts) - 2];
                    $city = $parts[count($parts) - 3];
                    $line = count($parts) > 3 ? implode(', ', array_slice($parts, 0, -3)) : null;

                    $updates = [];
                    if (trim((string) ($row->address_country_name ?? '')) === '') {
                        $updates['address_country_name'] = $countryName;
                    }
                    if (trim((string) ($row->address_state ?? '')) === '') {
                        $updates['address_state'] = $state;
                    }
                    if (trim((string) ($row->address_city ?? '')) === '') {
                        $updates['address_city'] = $city;
                    }
                    if ($line !== null && trim((string) ($row->address_line ?? '')) === '') {
                        $updates['address_line'] = $line;
                    }
                    if (trim((string) ($row->address_country_code ?? '')) === '') {
                        $resolvedCode = $countryCodeByLabel[strtolower($countryName)] ?? null;
                        if ($resolvedCode !== null) {
                            $updates['address_country_code'] = $resolvedCode;
                        }
                    }

                    if ($updates !== []) {
                        DB::table('gyms')
                            ->where('id', (int) $row->id)
                            ->update($updates);
                    }
                }
            });
    }

    public function down(): void
    {
        $columns = [
            'address_country_code',
            'address_country_name',
            'address_state',
            'address_city',
            'address_line',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('gyms', $column)) {
                Schema::table('gyms', function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
            }
        }
    }
};

