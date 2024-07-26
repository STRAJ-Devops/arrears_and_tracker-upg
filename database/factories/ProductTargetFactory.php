<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductTarget>
 */
class ProductTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //get a list of all product IDs from the database
        $productIds = \App\Models\Product::pluck('product_id')->toArray();
        return [
            'product_id' => $this->faker->randomElement($productIds),
            'target_amount' => $this->faker->randomNumber(2) * 1000,
        ];
    }
}
