<?php

namespace Database\Seeders;

use App\Models\FiscalYear;
use App\Models\Quarter;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class QuarterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create fiscal year 2025-2026
        $fiscalYear = FiscalYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
        ]);

        // Create quarters for 2025-2026
        $quarters = [
            [
                'name' => 'Jul-Sept 2025',
                'code' => 'Q1',
                'quarter_number' => 1,
                'start_date' => '2025-07-01',
                'end_date' => '2025-09-30',
            ],
            [
                'name' => 'Oct-Dec 2025',
                'code' => 'Q2',
                'quarter_number' => 2,
                'start_date' => '2025-10-01',
                'end_date' => '2025-12-31',
            ],
            [
                'name' => 'Jan-Mar 2026',
                'code' => 'Q3',
                'quarter_number' => 3,
                'start_date' => '2026-01-01',
                'end_date' => '2026-03-31',
            ],
            [
                'name' => 'Apr-Jun 2026',
                'code' => 'Q4',
                'quarter_number' => 4,
                'start_date' => '2026-04-01',
                'end_date' => '2026-06-30',
            ],
        ];

        foreach ($quarters as $quarter) {
            Quarter::create([
                'fiscal_year_id' => $fiscalYear->id,
                'name' => $quarter['name'],
                'code' => $quarter['code'],
                'quarter_number' => $quarter['quarter_number'],
                'start_date' => $quarter['start_date'],
                'end_date' => $quarter['end_date'],
            ]);
        }

        // Also create quarters for 2024-2025 (previous year)
        $prevFiscalYear = FiscalYear::create([
            'name' => '2024-2025',
            'start_date' => '2024-07-01',
            'end_date' => '2025-06-30',
        ]);

        $prevQuarters = [
            [
                'name' => 'Jul-Sept 2024',
                'code' => 'Q1',
                'quarter_number' => 1,
                'start_date' => '2024-07-01',
                'end_date' => '2024-09-30',
            ],
            [
                'name' => 'Oct-Dec 2024',
                'code' => 'Q2',
                'quarter_number' => 2,
                'start_date' => '2024-10-01',
                'end_date' => '2024-12-31',
            ],
            [
                'name' => 'Jan-Mar 2025',
                'code' => 'Q3',
                'quarter_number' => 3,
                'start_date' => '2025-01-01',
                'end_date' => '2025-03-31',
            ],
            [
                'name' => 'Apr-Jun 2025',
                'code' => 'Q4',
                'quarter_number' => 4,
                'start_date' => '2025-04-01',
                'end_date' => '2025-06-30',
            ],
        ];

        foreach ($prevQuarters as $quarter) {
            Quarter::create([
                'fiscal_year_id' => $prevFiscalYear->id,
                'name' => $quarter['name'],
                'code' => $quarter['code'],
                'quarter_number' => $quarter['quarter_number'],
                'start_date' => $quarter['start_date'],
                'end_date' => $quarter['end_date'],
            ]);
        }
    }
}