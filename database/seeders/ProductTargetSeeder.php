<?php

namespace Database\Seeders;

use App\Models\ProductTarget;
use Illuminate\Database\Seeder;

class ProductTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductTarget::factory(10)->create();
    }
}
