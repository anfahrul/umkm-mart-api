<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\UmkmCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productCategory = Carbon::now()->timestamp . " " . $this->faker->unique()->word(3);
        $slugWithoutSpace = str_replace( " ", "-", $productCategory);
        $slug = strtolower($slugWithoutSpace);

        return [
            'name' => $productCategory,
            'slug' => $slug,
            'umkm_category_id' => UmkmCategory::all()->random()->id,
        ];
    }
}
