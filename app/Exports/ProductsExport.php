<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::query();

        // Apply filters if provided
        if (isset($this->filters['warehouse_id'])) {
            $query->where('warehouse_id', $this->filters['warehouse_id']);
        }

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Size',
            'Color',
            'Normal Price',
            'Sale Price',
            'Status',
            'Warehouse ID',
            'Stock Quantity',
            'Description',
            'Images',
            'WooCommerce ID',
            'WooCommerce Synced At',
            'Created At',
            'Updated At'
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->sku,
            $product->size,
            $product->color,
            $product->normal_price,
            $product->sale_price,
            $product->status,
            $product->warehouse_id,
            $product->stock_quantity,
            $product->description,
            $product->images,
            $product->woocommerce_id,
            $product->woocommerce_synced_at,
            $product->created_at,
            $product->updated_at,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 30,  // Name
            'C' => 20,  // SKU
            'D' => 15,  // Size
            'E' => 15,  // Color
            'F' => 15,  // Normal Price
            'G' => 15,  // Sale Price
            'H' => 15,  // Status
            'I' => 15,  // Warehouse ID
            'J' => 15,  // Stock Quantity
            'K' => 40,  // Description
            'L' => 30,  // Images
            'M' => 15,  // WooCommerce ID
            'N' => 25,  // WooCommerce Synced At
            'O' => 25,  // Created At
            'P' => 25,  // Updated At
        ];
    }
}
