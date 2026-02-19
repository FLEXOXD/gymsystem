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

    private function hasDuplicateCredentialValues(): bool
    {
        return DB::table('client_credentials')
            ->select('gym_id', 'value')
            ->groupBy('gym_id', 'value')
            ->havingRaw('COUNT(*) > 1')
            ->exists();
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Acelera listado/paginacion por gimnasio: WHERE gym_id = ? ORDER BY id DESC
            $table->index(['gym_id', 'id'], 'clients_gym_id_id_idx');
            // Acelera busquedas por gimnasio sobre nombre/apellido
            $table->index(['gym_id', 'first_name', 'last_name', 'id'], 'clients_gym_name_search_idx');
        });

        Schema::table('client_credentials', function (Blueprint $table) {
            // Hot path check-in: WHERE gym_id = ? AND value = ? AND status = 'active'
            $table->index(['gym_id', 'value', 'status'], 'client_credentials_gym_value_status_idx');
        });

        if (! $this->hasDuplicateCredentialValues()) {
            Schema::table('client_credentials', function (Blueprint $table) {
                // Evita ambiguedad de credencial repetida entre QR/RFID dentro del mismo gimnasio
                $table->unique(['gym_id', 'value'], 'client_credentials_gym_value_uniq');
            });
        }

        Schema::table('attendances', function (Blueprint $table) {
            // Acelera ultimos ingresos por gimnasio: ORDER BY date DESC, time DESC
            $table->index(['gym_id', 'date', 'time', 'id'], 'attendances_gym_date_time_id_idx');
            // Acelera historial por cliente en gimnasio
            $table->index(['gym_id', 'client_id', 'date', 'time'], 'attendances_gym_client_date_time_idx');
        });

        Schema::table('memberships', function (Blueprint $table) {
            // Hot path membresia vigente por cliente
            $table->index(
                ['gym_id', 'client_id', 'status', 'starts_at', 'ends_at'],
                'memberships_gym_client_status_start_end_idx'
            );
            // Reportes por estado/vencimiento
            $table->index(
                ['gym_id', 'status', 'ends_at', 'client_id'],
                'memberships_gym_status_end_client_idx'
            );
        });

        Schema::table('cash_sessions', function (Blueprint $table) {
            // Caja abierta por gimnasio: WHERE gym_id = ? AND status = 'open' ORDER BY id DESC
            $table->index(['gym_id', 'status', 'id'], 'cash_sessions_gym_status_id_idx');
        });

        Schema::table('cash_movements', function (Blueprint $table) {
            // Reportes por fecha/metodo/tipo
            $table->index(
                ['gym_id', 'occurred_at', 'method', 'type'],
                'cash_movements_gym_occurred_method_type_idx'
            );
            // Totales por sesion de caja (resumen + metodos)
            $table->index(
                ['gym_id', 'cash_session_id', 'type', 'method'],
                'cash_movements_gym_session_type_method_idx'
            );
            // Pagos recientes por membresia del cliente
            $table->index(
                ['gym_id', 'membership_id', 'type', 'occurred_at'],
                'cash_movements_gym_membership_type_occurred_idx'
            );
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            // KPI SuperAdmin: conteo por estado y proximidad de vencimiento
            $table->index(['status', 'ends_at'], 'subscriptions_status_ends_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if ($this->hasIndex('clients', 'clients_gym_id_id_idx')) {
                $table->dropIndex('clients_gym_id_id_idx');
            }
            if ($this->hasIndex('clients', 'clients_gym_name_search_idx')) {
                $table->dropIndex('clients_gym_name_search_idx');
            }
        });

        Schema::table('client_credentials', function (Blueprint $table) {
            if ($this->hasIndex('client_credentials', 'client_credentials_gym_value_status_idx')) {
                $table->dropIndex('client_credentials_gym_value_status_idx');
            }
            if ($this->hasIndex('client_credentials', 'client_credentials_gym_value_uniq')) {
                $table->dropUnique('client_credentials_gym_value_uniq');
            }
        });

        Schema::table('attendances', function (Blueprint $table) {
            if ($this->hasIndex('attendances', 'attendances_gym_date_time_id_idx')) {
                $table->dropIndex('attendances_gym_date_time_id_idx');
            }
            if ($this->hasIndex('attendances', 'attendances_gym_client_date_time_idx')) {
                $table->dropIndex('attendances_gym_client_date_time_idx');
            }
        });

        Schema::table('memberships', function (Blueprint $table) {
            if ($this->hasIndex('memberships', 'memberships_gym_client_status_start_end_idx')) {
                $table->dropIndex('memberships_gym_client_status_start_end_idx');
            }
            if ($this->hasIndex('memberships', 'memberships_gym_status_end_client_idx')) {
                $table->dropIndex('memberships_gym_status_end_client_idx');
            }
        });

        Schema::table('cash_sessions', function (Blueprint $table) {
            if ($this->hasIndex('cash_sessions', 'cash_sessions_gym_status_id_idx')) {
                $table->dropIndex('cash_sessions_gym_status_id_idx');
            }
        });

        Schema::table('cash_movements', function (Blueprint $table) {
            if ($this->hasIndex('cash_movements', 'cash_movements_gym_occurred_method_type_idx')) {
                $table->dropIndex('cash_movements_gym_occurred_method_type_idx');
            }
            if ($this->hasIndex('cash_movements', 'cash_movements_gym_session_type_method_idx')) {
                $table->dropIndex('cash_movements_gym_session_type_method_idx');
            }
            if ($this->hasIndex('cash_movements', 'cash_movements_gym_membership_type_occurred_idx')) {
                $table->dropIndex('cash_movements_gym_membership_type_occurred_idx');
            }
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            if ($this->hasIndex('subscriptions', 'subscriptions_status_ends_at_idx')) {
                $table->dropIndex('subscriptions_status_ends_at_idx');
            }
        });
    }
};
