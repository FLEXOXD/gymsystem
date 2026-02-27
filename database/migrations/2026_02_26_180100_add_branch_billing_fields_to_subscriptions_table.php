<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table): void {
            if (! Schema::hasColumn('subscriptions', 'billing_owner_gym_id')) {
                $table->foreignId('billing_owner_gym_id')
                    ->nullable()
                    ->after('gym_id')
                    ->constrained('gyms')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('subscriptions', 'is_branch_managed')) {
                $table->boolean('is_branch_managed')
                    ->default(false)
                    ->after('billing_owner_gym_id');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('subscriptions', 'billing_owner_gym_id')) {
            Schema::table('subscriptions', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('billing_owner_gym_id');
            });
        }

        if (Schema::hasColumn('subscriptions', 'is_branch_managed')) {
            Schema::table('subscriptions', function (Blueprint $table): void {
                $table->dropColumn('is_branch_managed');
            });
        }
    }
};

