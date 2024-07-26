<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BranchTarget>
 */
class BranchTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //get a list of all branch IDs from the database
        $branchIds = \App\Models\Branch::pluck('branch_id')->toArray();
        return [
            'branch_id' => $this->faker->randomElement($branchIds),
            'target_amount' => $this->faker->randomNumber(2) * 1000,
            'target_numbers' => $this->faker->randomNumber(2) * 100,
        ];
    }
}
