<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' =>$this->faker->name(),
            'sku' => $this->faker->bothify('SKU-##########'),
            'size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL']),
            'color' => $this->faker->safeColorName(),
            'category' => $this->faker->randomElement(['Watches' , 'Caps' , 'Bags' , 'Wallets']),
            'normal_price' => strval($this->faker->randomFloat(2, 10, 100)),
            'sale_price' => strval($this->faker->randomFloat(2, 10, 100)),
            'status' => $this->faker->randomElement(['Published', 'Hidden']),
            'warehouse_id' => "2",
            'stock_quantity' => $this->faker->randomNumber(3),
            'description' => $this->faker->text(),
            'images' => $this->faker->imageUrl(),
        ];
    }
}
