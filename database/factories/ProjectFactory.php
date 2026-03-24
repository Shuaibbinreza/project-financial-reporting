<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'code' => strtoupper($this->faker->bothify('PRJ###')),
            'start_date' => now()->subMonths(rand(1, 12)),
            'end_date' => now()->addMonths(rand(6, 12)),
        ];
    }
}
