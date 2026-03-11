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
        Schema::create('product_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('cash_session_id')->nullable()->constrained('cash_sessions')->nullOnDelete();
            $table->foreignId('cash_movement_id')->nullable()->constrained('cash_movements')->nullOnDelete();
            $table->foreignId('sold_by')->constrained('users');
            $table->string('payment_method', 20)->default('cash');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('total_profit', 10, 2)->default(0);
            $table->string('notes', 255)->nullable();
            $table->dateTime('sold_at')->useCurrent();
            $table->timestamps();

            $table->index(['gym_id', 'sold_at']);
            $table->index(['gym_id', 'product_id']);
            $table->index(['gym_id', 'sold_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sales');
    }
};
