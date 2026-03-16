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
        Schema::table('product_stock_movements', function (Blueprint $table): void {
            if (! Schema::hasColumn('product_stock_movements', 'cash_movement_id')) {
                $table->foreignId('cash_movement_id')
                    ->nullable()
                    ->after('product_sale_id')
                    ->constrained('cash_movements')
                    ->nullOnDelete();
                $table->index(['gym_id', 'cash_movement_id'], 'product_stock_movements_gym_cash_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_stock_movements', function (Blueprint $table): void {
            if (Schema::hasColumn('product_stock_movements', 'cash_movement_id')) {
                $table->dropIndex('product_stock_movements_gym_cash_idx');
                $table->dropConstrainedForeignId('cash_movement_id');
            }
        });
    }
};

