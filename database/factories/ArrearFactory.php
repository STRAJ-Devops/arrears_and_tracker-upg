<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\District;
use App\Models\Officer;
use App\Models\Product;
use App\Models\Region;
use App\Models\Staff; // Import the Staff model
use App\Models\Sub_County;
use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArrearFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Arrear::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Retrieve all staff IDs from the database
        $staffIds = Officer::pluck('staff_id')->toArray();

        //retrieve all branch IDs from the database
        $branchIds = Branch::pluck('branch_id')->toArray();

        //retrieve all region IDs from the database
        $regionIds = Region::pluck('region_id')->toArray();

        //retrieve all product IDs from the database
        $productIds = Product::pluck('product_id')->toArray();

        //retrieve all village IDs from the database
        $villageIds = Village::pluck('village_id')->toArray();

        //retrieve all subcounty IDs from the database
        $subcountyIds = Sub_County::pluck('subcounty_id')->toArray();

        //retrieve all district IDs from the database
        $districtIds = District::pluck('district_id')->toArray();

        return [
            'staff_id' => $this->faker->randomElement($staffIds), // Randomly select a staff ID from the array
            'outsanding_principal' => $this->faker->randomNumber(2) * 100, // Random integer between 100 and 10000
            'outstanding_interest' => $this->faker->randomNumber(2) * 100, // Random integer between 100 and 10000
            'principal_arrears' => $this->faker->randomNumber(2) * 100, // Random integer between 100 and 10000
            'number_of_days_late' => $this->faker->randomNumber(2),
            'number_of_group_members' => $this->faker->randomNumber(1, 25),
            'lending_type' => $this->faker->randomElement(['individual', 'group']), // Randomly select 'individual' or 'group
            'branch_id' => $this->faker->randomElement($branchIds), // Randomly select a branch ID from the array
            'region_id' => $this->faker->randomElement($regionIds), // Randomly select a region ID from the array
            'product_id' => $this->faker->randomElement($productIds), // Randomly select a product ID from the array
            'district_id' => $this->faker->randomElement($districtIds), // Randomly select a district ID from the array
            'subcounty_id' => $this->faker->randomElement($subcountyIds), // Randomly select a subcounty ID from the array
            'village_id' => $this->faker->randomElement($villageIds), // Randomly select a village ID from the array
            'gender' => $this->faker->randomElement(['male', 'female']),
            'par' => $this->faker->randomNumber(2),
        ];
    }
}
