<?php

namespace Database\Factories;

use App\Models\Bank;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bank>
 */
class BankFactory extends Factory
{
    protected $model = Bank::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->company() . ' Bank',
            'country' => $this->faker->country(),
            'bank_code' => strtoupper($this->faker->bothify('????###')),
            'notes' => $this->faker->optional()->sentence(),
            'sort_order' => 1,
        ];
    }
}




