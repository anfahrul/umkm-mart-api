<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UmkmCategory;
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
        $merchantName = $this->faker->company();
        $domainWithoutSpace = str_replace( " ", "-", $merchantName);
        $domain = strtolower($domainWithoutSpace);

        return [
            'merchant_id' => $this->faker->uuid(),
            'user_id' => User::first()->id,
            // 'user_id' => $this->faker->unique()->randomElement([1, 2, 3, 4, 5]),
            'merchant_name' => $merchantName,
            'umkm_category_id' => UmkmCategory::all()->random()->id,
            'domain' => $domain,
            'address' => $this->faker->address(),
            'is_open' => $this->faker->randomElement([true, false]),
            'wa_number' => $this->faker->phoneNumber(),
            'merchant_website_url' => $this->faker->domainName(),
            'is_verified' => $this->faker->randomElement([true, false]),
            'original_logo_url' => '-',
            'operational_time_oneday' => $this->faker->numberBetween(1, 24),
            'description' => $this->faker->paragraph(),
        ];
    }
}
