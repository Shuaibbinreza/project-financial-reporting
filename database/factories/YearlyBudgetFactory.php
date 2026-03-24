<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\YearlyBudget;

class YearlyBudgetFactory extends Factory
{
    protected $model = YearlyBudget::class;

    public function definition()
    {
        return [
            'project_id' => \App\Models\Project::factory(),
            'fiscal_year_id' => \App\Models\FiscalYear::factory(),
            'category_id' => \App\Models\Category::factory(),
            'economic_code_id' => \App\Models\EconomicCode::factory(),
            'total_amount' => $this->faker->numberBetween(5000, 50000),
        ];
    }
}