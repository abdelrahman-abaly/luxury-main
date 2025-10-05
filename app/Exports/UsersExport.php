<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::query();

        // Apply filters if provided
        if (isset($this->filters['role_id'])) {
            $query->where('role_id', $this->filters['role_id']);
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
            'User ID',
            'Name',
            'Email',
            'Role ID',
            'Avatar',
            'Created At',
            'Updated At'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->user_id,
            $user->name,
            $user->email,
            $user->role_id,
            $user->avatar,
            $user->created_at,
            $user->updated_at,
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
            'B' => 15,  // User ID
            'C' => 30,  // Name
            'D' => 35,  // Email
            'E' => 15,  // Role ID
            'F' => 20,  // Avatar
            'G' => 25,  // Created At
            'H' => 25,  // Updated At
        ];
    }
}
