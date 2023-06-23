<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::factory()
        ->count(10000)
        ->create();
    }
}
