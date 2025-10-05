<?php

namespace App\Exports;

use App\Models\ScheduledTask;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScheduledTasksExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = ScheduledTask::query();

        // Apply filters if provided
        if (isset($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (isset($this->filters['task_done'])) {
            $query->where('task_done', $this->filters['task_done']);
        }

        if (isset($this->filters['date_from'])) {
            $query->whereDate('task_date', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to'])) {
            $query->whereDate('task_date', '<=', $this->filters['date_to']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User ID',
            'Lead ID',
            'Task Done',
            'Complete Date',
            'Task Date',
            'Created At',
            'Updated At'
        ];
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->user_id,
            $task->lead_id,
            $task->task_done,
            $task->complete_date,
            $task->task_date,
            $task->created_at,
            $task->updated_at,
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
            'C' => 15,  // Lead ID
            'D' => 15,  // Task Done
            'E' => 25,  // Complete Date
            'F' => 25,  // Task Date
            'G' => 25,  // Created At
            'H' => 25,  // Updated At
        ];
    }
}
