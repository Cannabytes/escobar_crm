<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'country' => $this->faker->country(),
            'moderator_id' => null,
            'license_file' => null,
        ];
    }

    public function withModerator(User $moderator): self
    {
        return $this->state(fn () => [
            'moderator_id' => $moderator->id,
        ]);
    }
}



