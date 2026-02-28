<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table): void {
            if (! Schema::hasColumn('subscriptions', 'sucursales_intro_pending')) {
                $table->boolean('sucursales_intro_pending')
                    ->default(false)
                    ->after('price');
            }
            if (! Schema::hasColumn('subscriptions', 'sucursales_base_price')) {
                $table->decimal('sucursales_base_price', 10, 2)
                    ->nullable()
                    ->after('sucursales_intro_pending');
            }
            if (! Schema::hasColumn('subscriptions', 'sucursales_intro_discount_percent')) {
                $table->unsignedTinyInteger('sucursales_intro_discount_percent')
                    ->nullable()
                    ->after('sucursales_base_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table): void {
            if (Schema::hasColumn('subscriptions', 'sucursales_intro_discount_percent')) {
                $table->dropColumn('sucursales_intro_discount_percent');
            }
            if (Schema::hasColumn('subscriptions', 'sucursales_base_price')) {
                $table->dropColumn('sucursales_base_price');
            }
            if (Schema::hasColumn('subscriptions', 'sucursales_intro_pending')) {
                $table->dropColumn('sucursales_intro_pending');
            }
        });
    }
};

