<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table): void {
            $table->decimal('price', 10, 2)->default(0)->after('plan_id');
            $table->foreignId('promotion_id')->nullable()->after('price')->constrained()->nullOnDelete();
            $table->string('promotion_name', 120)->nullable()->after('promotion_id');
            $table->string('promotion_type', 40)->nullable()->after('promotion_name');
            $table->decimal('promotion_value', 10, 2)->nullable()->after('promotion_type');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('promotion_value');
            $table->unsignedInteger('bonus_days')->default(0)->after('discount_amount');
        });

        DB::statement('
            UPDATE memberships
            SET price = COALESCE(
                (SELECT price FROM plans WHERE plans.id = memberships.plan_id),
                0
            )
            WHERE price = 0
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('promotion_id');
            $table->dropColumn([
                'price',
                'promotion_name',
                'promotion_type',
                'promotion_value',
                'discount_amount',
                'bonus_days',
            ]);
        });
    }
};
