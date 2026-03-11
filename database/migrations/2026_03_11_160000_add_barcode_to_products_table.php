<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode', 80)->nullable()->after('sku');
        });

        DB::table('products')
            ->select(['id', 'gym_id', 'name', 'category', 'sku', 'barcode'])
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    $sku = $this->normalizeCode($product->sku);
                    if ($sku === null) {
                        $sku = $this->generateSku(
                            (string) ($product->category ?? ''),
                            (string) ($product->name ?? ''),
                            (int) $product->id
                        );
                    }

                    $barcode = $this->normalizeCode($product->barcode);
                    if ($barcode === null) {
                        $barcode = $this->generateBarcode(
                            (int) $product->gym_id,
                            (int) $product->id
                        );
                    }

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'sku' => $sku,
                            'barcode' => $barcode,
                        ]);
                }
            });

        Schema::table('products', function (Blueprint $table) {
            $table->unique(['gym_id', 'barcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['gym_id', 'barcode']);
            $table->dropColumn('barcode');
        });
    }

    private function normalizeCode(mixed $value): ?string
    {
        $normalized = strtoupper(trim((string) $value));

        return $normalized !== '' ? $normalized : null;
    }

    private function generateSku(string $category, string $name, int $productId): string
    {
        $categoryChunk = $this->toCodeChunk($category, 'PRD', 3);
        $nameChunk = $this->toCodeChunk($name, 'ITEM', 4);

        return sprintf('%s-%s-%06d', $categoryChunk, $nameChunk, $productId);
    }

    private function generateBarcode(int $gymId, int $productId): string
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
};
