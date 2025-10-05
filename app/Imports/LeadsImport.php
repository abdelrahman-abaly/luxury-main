<?php

namespace App\Imports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LeadsImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    public function model(array $row)
    {
        return new Lead([
            'lead_id' => $row['lead_id'] ?? null,
            'name' => $row['name'],
            'phone_numbers' => $row['phone_numbers'] ?? '',
            'email' => $row['email'] ?? '',
            'governorate' => $row['governorate'] ?? '',
            'interested_categories' => $row['interested_categories'] ?? '',
            'interested_products_skus' => $row['interested_products_skus'] ?? '',
            'source' => $row['source'] ?? '',
            'degree_of_interest' => $row['degree_of_interest'] ?? 'Cold',
            'next_follow_up_period' => $row['next_follow_up_period'] ?? '',
            'potential' => $row['potential'] ?? '0',
            'added_by' => $row['added_by'] ?? '',
            'assigned_to' => $row['assigned_to'] ?? '',
            'notes' => $row['notes'] ?? '',
            'is_customer' => $row['is_customer'] ?? '0',
        ]);
    }

    public function rules(): array
    {
        return [
            'lead_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'phone_numbers' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'governorate' => 'nullable|string|max:255',
            'interested_categories' => 'nullable|string',
            'interested_products_skus' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'degree_of_interest' => 'nullable|string|in:Cold,Warm,Hot',
            'next_follow_up_period' => 'nullable|string|max:255',
            'potential' => 'nullable|numeric|min:0',
            'added_by' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_customer' => 'nullable|string|in:0,1',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
