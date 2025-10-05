<?php

namespace App\Imports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class OrdersImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    public function model(array $row)
    {
        return new Order([
            'order_number' => $row['order_number'],
            'customer_id' => $row['customer_id'] ?? '',
            'status' => $row['status'] ?? '1',
            'address' => $row['address'] ?? '',
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'notes' => $row['notes'] ?? null,
            'total' => $row['total'] ?? '0',
            'employee_commission' => $row['employee_commission'] ?? '0',
            'governorate' => $row['governorate'] ?? null,
            'coupon_code' => $row['coupon_code'] ?? null,
            'delivery_agent_id' => $row['delivery_agent_id'] ?? null,
            'employee_id' => $row['employee_id'] ?? null,
            'woocommerce_id' => $row['woocommerce_id'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'order_number' => 'required|string|max:255',
            'customer_id' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'total' => 'nullable|numeric|min:0',
            'employee_commission' => 'nullable|numeric|min:0',
            'governorate' => 'nullable|string|max:255',
            'coupon_code' => 'nullable|string|max:255',
            'delivery_agent_id' => 'nullable|string|max:255',
            'employee_id' => 'nullable|string|max:255',
            'woocommerce_id' => 'nullable|string|max:255',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
