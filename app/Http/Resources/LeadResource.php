<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class LeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $added_by = User::where("user_id", $this->added_by)->first();
        $assigned_to = User::where("user_id", $this->assigned_to)->first();
        if($this->is_customer === "0") {
            $createDate = Carbon::parse($this->created_at);
            $now = Carbon::now();
            $daysDifference = $now->diffInDays($createDate, true);
            $days_remaining = number_format(90 - $daysDifference);
        } else {
            $days_remaining = "0";
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone_numbers' => $this->phone_numbers,
            'email' => $this->email,
            'governorate' => $this->governorate,
            'interested_categories' => $this->interested_categories,
            'interested_products_skus' => $this->interested_products_skus,
            'lead_id' => $this->lead_id,
            'source' => $this->source,
            'degree_of_interest' => $this->degree_of_interest,
            'next_follow_up_period' => $this->next_follow_up_period,
            'potential' => $this->potential,
            'added_by' => $added_by,
            'assigned_to' => $assigned_to,
            'days_remaining' => $days_remaining,
            'notes' => $this->notes,
            'is_customer' => $this->is_customer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
