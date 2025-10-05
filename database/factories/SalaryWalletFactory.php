<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalaryWallet>
 */
class SalaryWalletFactory extends Factory
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
            'days_worked' => $this->faker->numberBetween(15, 31),
            'salary_wallet' => $this->faker->numberBetween(1000, 99999999),
            'ready_salary' => $this->faker->numberBetween(1000, 99999999),
            'borrowing_balance' => $this->faker->numberBetween(1000, 99999999),
            'user_id' => $this->faker->randomElement($users),
            "month" => $this->faker->monthName,
        ];
    }
}
