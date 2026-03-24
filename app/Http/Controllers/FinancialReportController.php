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

    public function cutoffReport(Request $request)
    {
        // Cutoff date
        $cutoffDate = $request->date ? \Carbon\Carbon::parse($request->date) : \Carbon\Carbon::create(2025, 9, 30);

        // Optional project filter
        $projectId = $request->project_id;

        // Get yearly budgets
        $yearlyBudgets = \App\Models\YearlyBudget::query()
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->with(['project', 'category'])
            ->get();

        $report = [];

        foreach ($yearlyBudgets as $budget) {

            $category = $budget->category->name;
            $projectName = $budget->project->name;

            // Total expenses as of cutoff date
            $expenses = \App\Models\VoucherEntry::where('category_id', $budget->category_id)
                ->where('economic_code_id', $budget->economic_code_id)
                ->whereHas('voucher', function ($q) use ($budget, $cutoffDate) {
                    $q->where('project_id', $budget->project_id)
                    ->whereDate('date', '<=', $cutoffDate);
                })
                ->sum('amount');

            $budgetAmount = $budget->total_amount;
            $budgetedPercentage = $budgetAmount > 0 ? ($expenses / $budgetAmount) * 100 : 0;

            // Total project budget
            $projectTotalBudget = \App\Models\YearlyBudget::where('project_id', $budget->project_id)
                ->sum('total_amount');

            $projectImplementationPercentage = $projectTotalBudget > 0
                ? ($expenses / $projectTotalBudget) * 100
                : 0;

            $report[] = [
                'project' => $projectName,
                'category' => $category,
                'expenses' => $expenses,
                'budget' => $budgetAmount,
                'budgeted_percentage' => round($budgetedPercentage, 2) . '%',
                'total_project_budget' => $projectTotalBudget,
                'project_implementation' => round($projectImplementationPercentage, 2) . '%',
            ];
        }

        return view('reports.cutoff', compact('report', 'cutoffDate'));
    }

    public function categorySummary(Request $request)
{
    // Cutoff date
    $cutoffDate = $request->date ? \Carbon\Carbon::parse($request->date) : \Carbon\Carbon::create(2025, 9, 30);

    // Optional filters: division/district
    $divisionIds = $request->division_ids;
    $districtIds = $request->district_ids;

    // Get all categories
    $categories = \App\Models\Category::all();

    $report = [];

    // Total project budget (all projects combined)
    $totalProjectBudget = \App\Models\YearlyBudget::sum('total_amount');

    foreach ($categories as $category) {

        // Total expenses for this category as of cutoff date
        $expenses = \App\Models\VoucherEntry::where('category_id', $category->id)
            ->whereHas('voucher', function ($q) use ($cutoffDate, $divisionIds, $districtIds) {
                $q->whereDate('date', '<=', $cutoffDate)
                  ->when($divisionIds, fn($q) => $q->whereIn('division_id', $divisionIds))
                  ->when($districtIds, fn($q) => $q->whereIn('district_id', $districtIds));
            })
            ->sum('amount');

        // Total budget for this category (all projects)
        $categoryBudget = \App\Models\YearlyBudget::where('category_id', $category->id)->sum('total_amount');

        $budgetedPercentage = $categoryBudget > 0 ? ($expenses / $categoryBudget) * 100 : 0;
        $projectImplementation = $totalProjectBudget > 0 ? ($expenses / $totalProjectBudget) * 100 : 0;

        $report[] = [
            'category' => $category->name,
            'expenses' => $expenses,
            'budget' => $categoryBudget,
            'budgeted_percentage' => round($budgetedPercentage, 2) . '%',
            'total_project_budget' => $totalProjectBudget,
            'project_implementation' => round($projectImplementation, 2) . '%',
        ];
    }

    return view('reports.category_summary', compact('report', 'cutoffDate'));
}
}