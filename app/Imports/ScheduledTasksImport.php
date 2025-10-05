<?php

namespace App\Imports;

use App\Models\ScheduledTask;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ScheduledTasksImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    public function model(array $row)
    {
        return new ScheduledTask([
            'user_id' => $row['user_id'],
            'lead_id' => $row['lead_id'] ?? null,
            'task_done' => $row['task_done'] ?? '0',
            'complete_date' => $row['complete_date'] ?? null,
            'task_date' => $row['task_date'] ?? now(),
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|string|max:255',
            'lead_id' => 'nullable|string|max:255',
            'task_done' => 'nullable|string|in:0,1',
            'complete_date' => 'nullable|date',
            'task_date' => 'nullable|date',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
