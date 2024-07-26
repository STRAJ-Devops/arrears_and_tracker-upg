<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Get product ids from the products table using ORM
        $productIds = \App\Models\Product::pluck('product_id')->toArray();

        // Get staff ids from the staff table using ORM
        $staffIds = \App\Models\Officer::pluck('staff_id')->toArray();


        //retrieve all branch IDs from the database
        $branchIds = Branch::pluck('branch_id')->toArray();

        //retrieve all region IDs from the database
        $regionIds = Region::pluck('region_id')->toArray();



        return [
            'staff_id' => $this->faker->randomElement($staffIds),
            'product_id' => $this->faker->randomElement($productIds),
            'disbursement_date' => $this->faker->date(),
            'disbursement_amount' => $this->faker->randomNumber(2) * 1000,
            'region_id' => $this->faker->randomElement($regionIds),
            'branch_id' => $this->faker->randomElement($branchIds),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'number_of_children' => $this->faker->randomNumber(1, 25),
        ];
    }
}
