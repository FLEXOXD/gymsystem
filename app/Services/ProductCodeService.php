<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductCodeService
{
    public function normalizeCode(mixed $value): ?string
    {
        $normalized = strtoupper(trim((string) $value));

        return $normalized !== '' ? $normalized : null;
    }

    public function ensureCodes(Product $product, mixed $sku = null, mixed $barcode = null): Product
    {
        $resolvedSku = $this->normalizeCode($sku) ?? $this->normalizeCode($product->sku);
        $resolvedBarcode = $this->normalizeCode($barcode) ?? $this->normalizeCode($product->barcode);

        if ($resolvedSku === null) {
            $resolvedSku = $this->generateSku(
                category: (string) ($product->category ?? ''),
                name: (string) ($product->name ?? ''),
                productId: (int) $product->id
            );
        }

        if ($resolvedBarcode === null) {
            $resolvedBarcode = $this->generateBarcode(
                gymId: (int) $product->gym_id,
                productId: (int) $product->id
            );
        }

        $updates = [];

        if ((string) $product->sku !== $resolvedSku) {
            $updates['sku'] = $resolvedSku;
        }

        if ((string) ($product->barcode ?? '') !== $resolvedBarcode) {
            $updates['barcode'] = $resolvedBarcode;
        }

        if ($updates !== []) {
            $product->update($updates);
            $product->refresh();
        }

        return $product;
    }

    public function generateSku(string $category, string $name, int $productId): string
    {
        $categoryChunk = $this->toCodeChunk($category, 'PRD', 3);
        $nameChunk = $this->toCodeChunk($name, 'ITEM', 4);

        return sprintf('%s-%s-%06d', $categoryChunk, $nameChunk, $productId);
    }

    public function generateBarcode(int $gymId, int $productId): string
    {
        $base = '2'
            .str_pad((string) ($gymId % 10000), 4, '0', STR_PAD_LEFT)
            .str_pad((string) ($productId % 10000000), 7, '0', STR_PAD_LEFT);

        return $base.$this->calculateEan13Checksum($base);
    }

    private function calculateEan13Checksum(string $base12): int
    {
        $digits = str_split($base12);
        $sum = 0;

        foreach ($digits as $index => $digit) {
            $sum += ((int) $digit) * (($index % 2) === 0 ? 1 : 3);
        }

        return (10 - ($sum % 10)) % 10;
    }

    private function toCodeChunk(string $value, string $fallback, int $length): string
    {
        $ascii = Str::upper(Str::ascii($value));
        $clean = preg_replace('/[^A-Z0-9]+/', '', $ascii) ?? '';

        if ($clean === '') {
            $clean = $fallback;
        }

        return substr($clean, 0, $length);
    }
}
