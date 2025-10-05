<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionHistory>
 */
class TransactionHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::pluck("user_id")->all();
        $type = $this->faker->randomElement(["1", "2", "3"]);
        $amount = $this->faker->numberBetween(1000, 99999999);
        $balance = $this->faker->numberBetween(1000, 99999999);
        $newBalance = 0;
        if($type === "2") {
            $newBalance = $balance - $amount;
        } else {
            $newBalance = $balance + $amount;
        }
        return [
            'transaction_id' => "TX" . $this->faker->randomNumber(9, true),
            'type' => $type,
            'send_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(["1", "2", "3"]),
            'amount' => $amount,
            'balance' => $balance,
            'new_balance' => $newBalance,
            'user_id' => $this->faker->randomElement($users),
        ];
    }
}
