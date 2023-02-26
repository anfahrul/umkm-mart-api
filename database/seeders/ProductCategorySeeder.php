<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductCategory::factory()
            ->count(5)
            ->create();

        // ProductCategory::create([
        //     'name' => 'Kuliner',
        //     'slug' => 'kuliner',
        // ]);

        // ProductCategory::create([
        //     'name' => 'Kecantikan',
        //     'slug' => 'kecantikan',
        // ]);

        // ProductCategory::create([
        //     'name' => 'Kriya',
        //     'slug' => 'kriya',
        // ]);

        // ProductCategory::create([
        //     'name' => 'Agrikultur',
        //     'slug' => 'agrikultur',
        // ]);
    }
}
