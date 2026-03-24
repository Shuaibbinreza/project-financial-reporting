<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Voucher;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition()
    {
        $division = \App\Models\Division::inRandomOrder()->first();
        $district = $division ? $division->districts()->inRandomOrder()->first() : null;

        return [
            'project_id' => \App\Models\Project::factory(),
            'date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'division_id' => $division ? $division->id : null,
            'district_id' => $district ? $district->id : null,
            'created_by' => 1, // assume admin user
        ];
    }
}