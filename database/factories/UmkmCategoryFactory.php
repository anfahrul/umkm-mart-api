<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UmkmCategory>
 */
class UmkmCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $umkmCategory = Carbon::now()->timestamp . " " . $this->faker->unique()->word(3);
        $slugWithoutSpace = str_replace( " ", "-", $umkmCategory);
        $slug = strtolower($slugWithoutSpace);

        return [
            'name' => $umkmCategory,
            'slug' => $slug
        ];
    }
}
