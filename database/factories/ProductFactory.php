<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Merchant;
use App\Models\ProductCategory;


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
            'name' => $this->faker->sentence(3),
            'merchant_id' => Merchant::all()->random()->merchant_id,
            'product_category_id' => ProductCategory::all()->random()->id,
            'minimal_order' => $this->faker->numberBetween(1, 10),
            'short_desc' => $this->faker->paragraph(),
            'price_value' => $this->faker->numberBetween(15000, 150000),
            'stock_value' => $this->faker->numberBetween(1, 50),
        ];
    }
}
