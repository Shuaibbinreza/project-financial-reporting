<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FiscalYear;
use Carbon\Carbon;

class FiscalYearFactory extends Factory
{
    protected $model = FiscalYear::class;

    public function definition()
    {
        $start = Carbon::create(now()->year, 7, 1); // July 1
        $end = $start->copy()->addYear()->subDay(); // June 30 next year

        return [
            'name' => $start->format('Y') . '-' . $end->format('Y'),
            'start_date' => $start,
            'end_date' => $end,
        ];
    }
}