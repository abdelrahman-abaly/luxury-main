<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeadsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Lead::query();

        // Apply filters if provided
        if (isset($this->filters['assigned_to'])) {
            $query->where('assigned_to', $this->filters['assigned_to']);
        }

        if (isset($this->filters['degree_of_interest'])) {
            $query->where('degree_of_interest', $this->filters['degree_of_interest']);
        }

        if (isset($this->filters['is_customer'])) {
            $query->where('is_customer', $this->filters['is_customer']);
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
            'Lead ID',
            'Name',
            'Phone Numbers',
            'Email',
            'Governorate',
            'Interested Categories',
            'Interested Products SKUs',
            'Source',
            'Degree of Interest',
            'Next Follow Up Period',
            'Potential',
            'Added By',
            'Assigned To',
            'Notes',
            'Is Customer',
            'Created At',
            'Updated At'
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->lead_id,
            $lead->name,
            $lead->phone_numbers,
            $lead->email,
            $lead->governorate,
            $lead->interested_categories,
            $lead->interested_products_skus,
            $lead->source,
            $lead->degree_of_interest,
            $lead->next_follow_up_period,
            $lead->potential,
            $lead->added_by,
            $lead->assigned_to,
            $lead->notes,
            $lead->is_customer,
            $lead->created_at,
            $lead->updated_at,
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
            'B' => 15,  // Lead ID
            'C' => 30,  // Name
            'D' => 25,  // Phone Numbers
            'E' => 30,  // Email
            'F' => 20,  // Governorate
            'G' => 30,  // Interested Categories
            'H' => 30,  // Interested Products SKUs
            'I' => 20,  // Source
            'J' => 20,  // Degree of Interest
            'K' => 25,  // Next Follow Up Period
            'L' => 15,  // Potential
            'M' => 15,  // Added By
            'N' => 15,  // Assigned To
            'O' => 40,  // Notes
            'P' => 15,  // Is Customer
            'Q' => 25,  // Created At
            'R' => 25,  // Updated At
        ];
    }
}
