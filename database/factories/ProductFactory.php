<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Merchant;

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
            'product_id' => $this->faker->uuid(),
            'merchant_id' => Merchant::all()->random()->merchant_id,
            // 'product_category_id' => $this->faker->numberBetween(1, 4),
            'product_category_id' => 1,
            'name' => $this->faker->sentence(3),
            'price' => $this->faker->numberBetween(15000, 150000),
            'image' => '-',
            'description' => $this->faker->paragraph(),
        ];
    }
}