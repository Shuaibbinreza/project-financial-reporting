<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;
use App\Models\District;
use App\Models\Project;
use App\Models\Category;
use App\Models\EconomicCode;
use App\Models\FiscalYear;
use App\Models\YearlyBudget;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherEntry;

class FinancialDataSeeder extends Seeder
{
    public function run()
    {
        // -------------------------
        // 2. Users
        // -------------------------
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // -------------------------
        // 3. Projects
        // -------------------------
        $projects = Project::factory(2)->create();

        // -------------------------
        // 4. Categories + Economic Codes
        // -------------------------
        $categories = Category::factory(2)->create()->each(function ($category) {
            EconomicCode::factory(2)->create([
                'category_id' => $category->id
            ]);
        });

        // -------------------------
        // 5. Fiscal Year
        // -------------------------
        $fiscalYear = FiscalYear::factory()->create();

        // -------------------------
        // 6. Yearly Budgets
        // -------------------------
        foreach ($projects as $project) {
            foreach ($categories as $category) {
                foreach ($category->economicCodes as $ecoCode) {
                    YearlyBudget::factory()->create([
                        'project_id' => $project->id,
                        'fiscal_year_id' => $fiscalYear->id,
                        'category_id' => $category->id,
                        'economic_code_id' => $ecoCode->id,
                        'total_amount' => rand(10000, 50000),
                    ]);
                }
            }
        }

        // -------------------------
        // 7. Vouchers + Voucher Entries
        // -------------------------
        $divisions = Division::all();

        foreach ($projects as $project) {
            Voucher::factory(5)->create([
                'project_id' => $project->id,
                'created_by' => $user->id,
            ])->each(function ($voucher) use ($divisions, $categories) {
                $division = $divisions->random();
                $district = $division->districts->random();

                $voucher->update([
                    'division_id' => $division->id,
                    'district_id' => $district->id,
                ]);

                // Add Voucher Entries
                foreach ($categories as $category) {
                    foreach ($category->economicCodes as $ecoCode) {
                        VoucherEntry::factory()->create([
                            'voucher_id' => $voucher->id,
                            'category_id' => $category->id,
                            'economic_code_id' => $ecoCode->id,
                            'amount' => rand(500, 5000),
                        ]);
                    }
                }
            });
        }
    }
}