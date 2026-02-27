<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('legal_acceptances', function (Blueprint $table): void {
            if (! Schema::hasColumn('legal_acceptances', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('legal_acceptances', 'gym_id')) {
                $table->foreignId('gym_id')->nullable()->after('user_id')->constrained('gyms')->nullOnDelete();
            }
            if (! Schema::hasColumn('legal_acceptances', 'accepted_via')) {
                $table->string('accepted_via', 40)->nullable()->after('accepted');
            }
            if (! Schema::hasColumn('legal_acceptances', 'session_id')) {
                $table->string('session_id', 120)->nullable()->after('accepted_via');
            }
            if (! Schema::hasColumn('legal_acceptances', 'source_url')) {
                $table->string('source_url', 255)->nullable()->after('session_id');
            }
            if (! Schema::hasColumn('legal_acceptances', 'location_permission')) {
                $table->string('location_permission', 20)->nullable()->after('source_url');
            }
            if (! Schema::hasColumn('legal_acceptances', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('location_permission');
            }
            if (! Schema::hasColumn('legal_acceptances', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (! Schema::hasColumn('legal_acceptances', 'location_accuracy_m')) {
                $table->decimal('location_accuracy_m', 10, 2)->nullable()->after('longitude');
            }
            if (! Schema::hasColumn('legal_acceptances', 'contract_code')) {
                $table->string('contract_code', 64)->nullable()->after('location_accuracy_m');
            }
        });

        Schema::table('legal_acceptances', function (Blueprint $table): void {
            $table->index(['user_id', 'legal_version'], 'legal_acceptances_user_version_idx');
            $table->index('contract_code', 'legal_acceptances_contract_code_idx');
        });
    }

    public function down(): void
    {
        Schema::table('legal_acceptances', function (Blueprint $table): void {
            $table->dropIndex('legal_acceptances_user_version_idx');
            $table->dropIndex('legal_acceptances_contract_code_idx');

            if (Schema::hasColumn('legal_acceptances', 'contract_code')) {
                $table->dropColumn('contract_code');
            }
            if (Schema::hasColumn('legal_acceptances', 'location_accuracy_m')) {
                $table->dropColumn('location_accuracy_m');
            }
            if (Schema::hasColumn('legal_acceptances', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('legal_acceptances', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('legal_acceptances', 'location_permission')) {
                $table->dropColumn('location_permission');
            }
            if (Schema::hasColumn('legal_acceptances', 'source_url')) {
                $table->dropColumn('source_url');
            }
            if (Schema::hasColumn('legal_acceptances', 'session_id')) {
                $table->dropColumn('session_id');
            }
            if (Schema::hasColumn('legal_acceptances', 'accepted_via')) {
                $table->dropColumn('accepted_via');
            }
            if (Schema::hasColumn('legal_acceptances', 'gym_id')) {
                $table->dropConstrainedForeignId('gym_id');
            }
            if (Schema::hasColumn('legal_acceptances', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};

