<?php

namespace App\Http\Resources;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lead = Lead::where('lead_id', $this->lead_id)->first();
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "lead" => $lead,
            "task_done" => $this->task_done,
            "complete_date" => $this->complete_date,
            "task_date" => $this->task_date,
            "created_at" => $this->created_at->format('Y-m-d H:i:s'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
