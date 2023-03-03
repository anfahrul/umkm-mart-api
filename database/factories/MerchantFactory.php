<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductCategory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Merchant>
 */
class MerchantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'merchant_id' => $this->faker->uuid(),
            'name' => $this->faker->company(),
            'product_category_id' => ProductCategory::all()->random()->id,
            // 'user_id' => User::all()->random()->id,
            'user_id' => $this->faker->unique()->randomElement([1, 2, 3, 4, 5]),
            'address' => $this->faker->address(),
            'operational_time_oneday' => $this->faker->numberBetween(1, 24),
            'logo' => '-',
            'description' => $this->faker->paragraph(),
            'is_open' => $this->faker->randomElement([true, false]),
        ];
    }
}
