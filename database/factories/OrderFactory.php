<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $governorate = ["Cairo" ,"Alexandria" ,"Giza" ,"Aswan","Red Sea"];
        $status = ["1","2","3","4","5","6","7"];
        $customers = Lead::where("is_customer", "1")->pluck("lead_id")->all();
        $users = User::pluck("user_id")->all();
        return [
            'order_number' => $this->faker->unique()->randomNumber(),
            'customer_id' => $this->faker->randomElement($customers),
            'status' => $this->faker->randomElement($status),
            'address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'notes' => $this->faker->text(),
            'total' => $this->faker->randomFloat(),
            'employee_commission' => $this->faker->randomFloat(),
            'governorate' => $governorate[array_rand($governorate)],
            'coupon_code' => $this->faker->unique()->randomNumber(),
            'delivery_agent_id' => $this->faker->randomElement($users),
            'employee_id' => $this->faker->randomElement($users),
        ];
    }
}
