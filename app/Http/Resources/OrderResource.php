<?php

namespace App\Http\Resources;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customer = Lead::where("lead_id",$this->customer_id)->first();
        $employee = User::where("user_id",$this->employee_id)->first();
        $delivery_agent = User::where("user_id",$this->delivery_agent_id)->first();

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'customer' => $customer,
            'status' => $this->status,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'notes' => $this->notes,
            'total' => $this->total,
            'employee_commission' => $this->employee_commission,
            'governorate' => $this->governorate,
            'coupon_code' => $this->coupon_code,
            'delivery_agent' => $delivery_agent,
            'employee' => $employee,
            "created_at" => $this->created_at->format('Y-m-d H:i:s'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
