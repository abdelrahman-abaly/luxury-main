<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Order::query();

        // Apply filters if provided
        if (isset($this->filters['employee_id'])) {
            $query->where('employee_id', $this->filters['employee_id']);
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

        if (isset($this->filters['governorate'])) {
            $query->where('governorate', $this->filters['governorate']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Order Number',
            'Customer ID',
            'Status',
            'Address',
            'Latitude',
            'Longitude',
            'Notes',
            'Total',
            'Employee Commission',
            'Governorate',
            'Coupon Code',
            'Delivery Agent ID',
            'Employee ID',
            'WooCommerce ID',
            'WooCommerce Synced At',
            'Created At',
            'Updated At'
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->order_number,
            $order->customer_id,
            $order->status,
            $order->address,
            $order->latitude,
            $order->longitude,
            $order->notes,
            $order->total,
            $order->employee_commission,
            $order->governorate,
            $order->coupon_code,
            $order->delivery_agent_id,
            $order->employee_id,
            $order->woocommerce_id,
            $order->woocommerce_synced_at,
            $order->created_at,
            $order->updated_at,
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
            'B' => 20,  // Order Number
            'C' => 15,  // Customer ID
            'D' => 15,  // Status
            'E' => 40,  // Address
            'F' => 15,  // Latitude
            'G' => 15,  // Longitude
            'H' => 30,  // Notes
            'I' => 15,  // Total
            'J' => 20,  // Employee Commission
            'K' => 20,  // Governorate
            'L' => 20,  // Coupon Code
            'M' => 20,  // Delivery Agent ID
            'N' => 15,  // Employee ID
            'O' => 15,  // WooCommerce ID
            'P' => 25,  // WooCommerce Synced At
            'Q' => 25,  // Created At
            'R' => 25,  // Updated At
        ];
    }
}
