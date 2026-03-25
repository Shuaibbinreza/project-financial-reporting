<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Quarter;
use App\Models\FiscalYear;
use Carbon\Carbon;

class QuarterFactory extends Factory
{
    protected $model = Quarter::class;

    public function definition()
    {
        $fiscalYear = FiscalYear::factory()->create();
        $start = Carbon::parse($fiscalYear->start_date);
        
        return [
            'fiscal_year_id' => $fiscalYear->id,
            'name' => 'Jul-Sept ' . $start->format('Y'),
            'code' => 'Q1',
            'quarter_number' => 1,
            'start_date' => $start,
            'end_date' => $start->copy()->addMonths(3)->subDay(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Quarter $quarter) {
            $fiscalYear = $quarter->fiscalYear;
            $start = Carbon::parse($fiscalYear->start_date);
            
            // Create all 4 quarters for the fiscal year
            $quarters = [
                ['name' => 'Jul-Sept ' . $start->format('Y'), 'code' => 'Q1', 'quarter_number' => 1, 
                 'start_date' => $start, 'end_date' => $start->copy()->addMonths(3)->subDay()],
                ['name' => 'Oct-Dec ' . $start->format('Y'), 'code' => 'Q2', 'quarter_number' => 2,
                 'start_date' => $start->copy()->addMonths(3), 'end_date' => $start->copy()->addMonths(6)->subDay()],
                ['name' => 'Jan-Mar ' . (intval($start->format('Y')) + 1), 'code' => 'Q3', 'quarter_number' => 3,
                 'start_date' => $start->copy()->addMonths(6), 'end_date' => $start->copy()->addMonths(9)->subDay()],
                ['name' => 'Apr-Jun ' . (intval($start->format('Y')) + 1), 'code' => 'Q4', 'quarter_number' => 4,
                 'start_date' => $start->copy()->addMonths(9), 'end_date' => $start->copy()->addYear()->subDay()],
            ];
            
            foreach ($quarters as $q) {
                Quarter::create([
                    'fiscal_year_id' => $quarter->fiscal_year_id,
                    'name' => $q['name'],
                    'code' => $q['code'],
                    'quarter_number' => $q['quarter_number'],
                    'start_date' => $q['start_date'],
                    'end_date' => $q['end_date'],
                ]);
            }
        });
    }
}