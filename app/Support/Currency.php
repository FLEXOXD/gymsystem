<?php

namespace App\Support;

class Currency
{
    /**
     * @return array<string, array{name:string,symbol:string}>
     */
    public static function options(): array
    {
        return [
            'USD' => ['name' => 'US Dollar', 'symbol' => '$'],
            'EUR' => ['name' => 'Euro', 'symbol' => 'EUR '],
            'GBP' => ['name' => 'Pound Sterling', 'symbol' => 'GBP '],
            'MXN' => ['name' => 'Mexican Peso', 'symbol' => 'MXN '],
            'COP' => ['name' => 'Colombian Peso', 'symbol' => 'COP '],
            'PEN' => ['name' => 'Peruvian Sol', 'symbol' => 'S/ '],
            'ARS' => ['name' => 'Argentine Peso', 'symbol' => 'ARS '],
            'CLP' => ['name' => 'Chilean Peso', 'symbol' => 'CLP '],
            'BRL' => ['name' => 'Brazilian Real', 'symbol' => 'R$ '],
        ];
    }

    public static function normalizeCode(?string $code): string
    {
        $normalized = strtoupper(trim((string) $code));
        $options = self::options();

        return isset($options[$normalized]) ? $normalized : 'USD';
    }

    public static function symbol(?string $currencyCode): string
    {
        $code = self::normalizeCode($currencyCode);
        $options = self::options();

        return $options[$code]['symbol'] ?? ($code.' ');
    }

    public static function format(float|int|string|null $amount, ?string $currencyCode = null, bool $absolute = false): string
    {
        $value = (float) ($amount ?? 0);
        $isNegative = $value < 0;
        if ($absolute) {
            $value = abs($value);
            $isNegative = false;
        }

        $prefix = $isNegative ? '-' : '';

        return $prefix.self::symbol($currencyCode).number_format($value, 2);
    }
}
