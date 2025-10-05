<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommissionWallet>
 */
class CommissionWalletFactory extends Factory
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
            'on_processing' => $this->faker->numberBetween(1000, 99999999),
            'pending' => $this->faker->numberBetween(1000, 99999999),
            'ready_to_pay' => $this->faker->numberBetween(1000, 99999999),
            'completed' => $this->faker->numberBetween(1000, 99999999),
            'user_id' => $this->faker->randomElement($users),
        ];
    }
}
