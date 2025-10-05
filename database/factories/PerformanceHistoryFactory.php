<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PerformanceHistory>
 */
class PerformanceHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::pluck("user_id")->all();
        return [
            'level' => $this->faker->randomElement(['User' , 'Beginner' , 'Rising' , 'Expert', 'Pioneer', 'Professional']),
            'month' => $this->faker->monthName(),
            'year' => $this->faker->year(),
            'orders_count' => $this->faker->numberBetween(0, 9999),
            'commission_amount' => $this->faker->numberBetween(1000, 99999),
            'status' => $this->faker->randomElement(['Paid' , 'Processing']),
            'user_id' => $this->faker->randomElement($users),
        ];
    }
}
