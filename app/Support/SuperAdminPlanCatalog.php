<?php

namespace App\Support;

class SuperAdminPlanCatalog
{
    /**
     * @return array<int, array<string, int|string|float>>
     */
    public static function defaults(): array
    {
        return [
            [
                'plan_key' => 'basico',
                'name' => 'PLAN CONTROL',
                'duration_unit' => 'days',
                'duration_days' => 30,
                'duration_months' => 1,
                'price' => 25.00,
                'discount_price' => 19.00,
                'status' => 'active',
            ],
            [
                'plan_key' => 'profesional',
                'name' => 'PLAN CRECIMIENTO',
                'duration_unit' => 'days',
                'duration_days' => 30,
                'duration_months' => 1,
                'price' => 35.00,
                'discount_price' => 25.00,
                'status' => 'active',
            ],
            [
                'plan_key' => 'premium',
                'name' => 'PLAN ÉLITE',
                'duration_unit' => 'days',
                'duration_days' => 30,
                'duration_months' => 1,
                'price' => 50.00,
                'discount_price' => 35.00,
                'status' => 'active',
            ],
            [
                'plan_key' => 'sucursales',
                'name' => 'PLAN SUCURSALES',
                'duration_unit' => 'days',
                'duration_days' => 30,
                'duration_months' => 1,
                'price' => 90.00,
                'discount_price' => 45.00,
                'status' => 'active',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_values(array_map(
            static fn (array $plan): string => (string) $plan['plan_key'],
            self::defaults()
        ));
    }

    public static function orderCaseSql(string $column = 'plan_key'): string
    {
        $cases = [];
        foreach (self::defaults() as $index => $plan) {
            $key = str_replace("'", "''", (string) $plan['plan_key']);
            $cases[] = "WHEN '".$key."' THEN ".$index;
        }

        return 'CASE '.$column.' '.implode(' ', $cases).' ELSE 999 END';
    }
}
