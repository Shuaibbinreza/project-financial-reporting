<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EconomicCode;

class EconomicCodeFactory extends Factory
{
    protected $model = EconomicCode::class;

    public function definition()
    {
        return [
            'category_id' => \App\Models\Category::factory(),
            'code' => $this->faker->unique()->numerify('EC###'),
            'description' => $this->faker->sentence(3),
        ];
    }
}