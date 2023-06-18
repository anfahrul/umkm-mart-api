<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Merchant;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Merchant::factory()
            ->count(1)
            ->create();

        // Merchant::factory()
        //     ->count(2)
        //     ->hasProducts(3)
        //     ->create();

        // Merchant::factory()
        //     ->count(1)
        //     ->create();
    }
}
