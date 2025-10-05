<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $governorate = ["Cairo" ,"Alexandria" ,"Giza" ,"Aswan","Red Sea"];
        $categories = ["Watches" , "Caps" , "Bags" , "Wallets"];
        $sources = ['Whatsapp' , 'Phone' , 'Facebook' , 'Instagram' , 'Other'];
        $interests = ['Hot' , 'Warm'  , 'Cold' , 'Cancelled'];
        $users = ["4f40ffb1-67be-46a6-9f6f-0f3e57bb1606", "0a1555bd-b60d-4b76-999f-e0a43a07b34b", "f0807b34-636a-43fc-99d4-e5e5c2849aa7"];
        return [
            'name' => $this->faker->userName(),
            'phone_numbers' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'governorate' => $governorate[array_rand($governorate)],
            'interested_categories' => $categories[array_rand($categories)],
            'interested_products_skus' => $this->faker->randomDigit(),
            'lead_id' => $this->faker->uuid(),
            'source' => $sources[array_rand($sources)],
            'degree_of_interest' => $interests[array_rand($interests)],
            'next_follow_up_period' => $this->faker->date(),
            'potential' => $this->faker->randomFloat(nbMaxDecimals: 2,min: 200,max: 999999),
            'added_by' => $users[array_rand($users)],
            'assigned_to' => $users[array_rand($users)],
            'notes' => $this->faker->text(),
            'is_customer' => $this->faker->randomElement(["0","1"]),
        ];
    }
}
