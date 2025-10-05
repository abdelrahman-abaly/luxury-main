<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduledTask>
 */
class ScheduledTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::pluck("user_id")->all();
        $random_user = $this->faker->randomElement($users);
        $leads = Lead::where("is_customer", "0")->where("assigned_to",$random_user)
            ->pluck("lead_id")->all();
        $random_lead = $this->faker->randomElement($leads);
        return [
            "user_id" => $random_user,
            "lead_id" => $random_lead,
            "task_done" => $this->faker->randomElement(["1", "0"]),
            "complete_date" => $this->faker->dateTimeBetween('-1 year'),
            "task_date" => $this->faker->dateTimeBetween('-1 year'),
        ];
    }
}
