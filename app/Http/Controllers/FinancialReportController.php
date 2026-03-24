<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VoucherEntry;
use App\Models\YearlyBudget;
use App\Models\Project;
use App\Models\Category;
use App\Models\EconomicCode;
use App\Models\Division;
use App\Models\District;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        // -----------------------------
        // Filters
        // -----------------------------
        $projectId = $request->project_id;
        $divisionIds = $request->division_ids;
        $districtIds = $request->district_ids;
        $categoryIds = $request->category_ids;
        $economicCodeIds = $request->economic_code_ids;
        $fiscalYearId = $request->fiscal_year_id;
        $selectedQuarter = $request->quarter; // Q1, Q2, Q3, Q4 or null

        // -----------------------------
        // Fiscal year quarters
        // -----------------------------
        $quarterMonths = [
            'Q1' => [7, 8, 9],
            'Q2' => [10, 11, 12],
            'Q3' => [1, 2, 3],
            'Q4' => [4, 5, 6],
        ];

        if ($selectedQuarter) {
            $quarterMonths = [$selectedQuarter => $quarterMonths[$selectedQuarter]];
        }

        // -----------------------------
        // Get yearly budgets
        // -----------------------------
        $yearlyBudgets = YearlyBudget::query()
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->when($fiscalYearId, fn($q) => $q->where('fiscal_year_id', $fiscalYearId))
            ->when($categoryIds, fn($q) => $q->whereIn('category_id', $categoryIds))
            ->when($economicCodeIds, fn($q) => $q->whereIn('economic_code_id', $economicCodeIds))
            ->with(['project', 'category', 'economicCode'])
            ->get();

        $report = [];

        foreach ($yearlyBudgets as $budget) {

            $key = $budget->project->name . ' | ' . $budget->category->name . ' | ' . $budget->economicCode->code;

            $report[$key] = [
                'project' => $budget->project->name,
                'category' => $budget->category->name,
                'economic_code' => $budget->economicCode->code,
                'yearly_budget' => $budget->total_amount,
                'quarters' => [],
            ];

            $totalExpenses = 0;

            foreach ($quarterMonths as $qName => $months) {

                $quarterExpenses = VoucherEntry::where('category_id', $budget->category_id)
                    ->where('economic_code_id', $budget->economic_code_id)
                    ->whereHas('voucher', function ($q) use ($projectId, $divisionIds, $districtIds, $months) {
                        $q->when($projectId, fn($q) => $q->where('project_id', $projectId))
                          ->when($divisionIds, fn($q) => $q->whereIn('division_id', $divisionIds))
                          ->when($districtIds, fn($q) => $q->whereIn('district_id', $districtIds))
                          ->whereMonth('date', $months);
                    })
                    ->sum('amount');

                $quarterBudget = $budget->total_amount / 4; // evenly distributed
                $percentSpent = $quarterBudget > 0 ? ($quarterExpenses / $quarterBudget) * 100 : 0;

                $report[$key]['quarters'][$qName] = [
                    'budget' => $quarterBudget,
                    'expenses' => $quarterExpenses,
                    'percent_spent' => round($percentSpent, 2) . '%',
                ];

                $totalExpenses += $quarterExpenses;
            }

            $report[$key]['yearly_total'] = [
                'budget' => $budget->total_amount,
                'expenses' => $totalExpenses,
                'percent_spent' => $budget->total_amount > 0
                    ? round($totalExpenses / $budget->total_amount * 100, 2) . '%'
                    : '0%',
            ];
        }

        // -----------------------------
        // Pass to view
        // -----------------------------
        $projects = Project::all();
        $categories = Category::all();
        $economicCodes = EconomicCode::all();
        $divisions = Division::all();
        $districts = District::all();

        return view('reports.financial', compact(
            'report', 'projects', 'categories', 'economicCodes', 'divisions', 'districts'
        ));
    }
}