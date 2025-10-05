<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\Rule;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    public function model(array $row)
    {
        return new Product([
            'name' => $row['name'],
            'sku' => $row['sku'],
            'size' => $row['size'] ?? null,
            'color' => $row['color'] ?? null,
            'normal_price' => $row['normal_price'] ?? '0',
            'sale_price' => $row['sale_price'] ?? '0',
            'status' => $row['status'] ?? 'publish',
            'warehouse_id' => $row['warehouse_id'] ?? null,
            'stock_quantity' => $row['stock_quantity'] ?? '0',
            'description' => $row['description'] ?? '',
            'images' => $row['images'] ?? null,
            'woocommerce_id' => $row['woocommerce_id'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'normal_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:draft,pending,private,publish',
            'warehouse_id' => 'nullable|string|max:255',
            'stock_quantity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'images' => 'nullable|string',
            'woocommerce_id' => 'nullable|string|max:255',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
