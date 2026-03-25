<?php

namespace App\Services;

use App\Models\VoucherEntry;
use App\Models\YearlyBudget;
use App\Models\Project;
use App\Models\Quarter;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /**
     * Quarter months mapping (non-cumulative)
     */
    protected $quarterMonths = [
        'Q1' => [7, 8, 9],    // July, August, September
        'Q2' => [10, 11, 12], // October, November, December
        'Q3' => [1, 2, 3],    // January, February, March
        'Q4' => [4, 5, 6],    // April, May, June
    ];

    /**
     * Get project summary with nested quarter data
     */
    public function getProjectSummary($divisionId = null, $districtId = null, $selectedQuarter = null)
    {
        $showAllQuarters = (!$selectedQuarter || $selectedQuarter === 'all');

        // Get quarters from database for display names
        $quarters = Quarter::orderBy('quarter_number')->get();

        // Get all projects
        $projects = Project::all();

        $report = [];

        // Total budget across all projects
        $totalBudget = YearlyBudget::sum('total_amount');

        foreach ($projects as $project) {
            $projectQuarters = [];
            $projectTotalExpenses = 0;
            $projectBudget = YearlyBudget::where('project_id', $project->id)->sum('total_amount');

            foreach ($quarters as $quarter) {
                $qCode = $quarter->code;
                $months = $this->quarterMonths[$qCode] ?? [];

                if (empty($months)) continue;

                $expenses = $this->getExpensesForPeriod($project->id, $months, $divisionId, $districtId);

                // Quarterly budget (3 months = 3/12 of yearly budget)
                $quarterBudget = $projectBudget * (3 / 12);
                $budgetedPercentage = $quarterBudget > 0 ? ($expenses / $quarterBudget) * 100 : 0;

                $projectQuarters[] = [
                    'quarter_name' => $quarter->name,
                    'quarter_code' => $qCode,
                    'expenses' => $expenses,
                    'budget' => $quarterBudget,
                    'budgeted_percentage' => round($budgetedPercentage, 2) . '%',
                ];

                $projectTotalExpenses += $expenses;
            }

            // Calculate project totals
            $budgetedPercentageTotal = $projectBudget > 0 ? ($projectTotalExpenses / $projectBudget) * 100 : 0;
            $projectImplementation = $totalBudget > 0 ? ($projectTotalExpenses / $totalBudget) * 100 : 0;

            $report[] = [
                'project' => $project->name,
                'project_id' => $project->id,
                'total_budget' => $projectBudget,
                'total_expenses' => $projectTotalExpenses,
                'budgeted_percentage' => round($budgetedPercentageTotal, 2) . '%',
                'total_budget_all' => $totalBudget,
                'project_implementation' => round($projectImplementation, 2) . '%',
                'quarters' => $projectQuarters,
            ];
        }

        return [
            'report' => $report,
            'quarters' => $quarters->pluck('name', 'code')->toArray(),
            'showAllQuarters' => $showAllQuarters,
        ];
    }

    /**
     * Get category summary with nested quarter data
     */
    public function getCategorySummary($projectId = null, $divisionId = null, $districtId = null, $selectedQuarter = null)
    {
        $showAllQuarters = (!$selectedQuarter || $selectedQuarter === 'all');

        // Get quarters from database for display names
        $quarters = Quarter::orderBy('quarter_number')->get();

        $categories = \App\Models\Category::all();
        $report = [];

        // Total project budget (filtered by project if selected)
        $totalProjectBudgetQuery = YearlyBudget::query()
            ->when($projectId, fn($q) => $q->where('project_id', $projectId));

        $totalProjectBudget = $totalProjectBudgetQuery->sum('total_amount');

        foreach ($categories as $category) {
            $categoryQuarters = [];
            $categoryTotalExpenses = 0;
            $categoryBudget = YearlyBudget::where('category_id', $category->id)
                ->when($projectId, fn($q) => $q->where('project_id', $projectId))
                ->sum('total_amount');

            foreach ($quarters as $quarter) {
                $qCode = $quarter->code;
                $months = $this->quarterMonths[$qCode] ?? [];

                if (empty($months)) continue;

                $expenses = $this->getCategoryExpensesForPeriod($category->id, $months, $projectId, $divisionId, $districtId);

                // Quarterly budget (3 months = 3/12 of yearly budget)
                $quarterBudget = $categoryBudget * (3 / 12);
                $budgetedPercentage = $quarterBudget > 0 ? ($expenses / $quarterBudget) * 100 : 0;

                $categoryQuarters[] = [
                    'quarter_name' => $quarter->name,
                    'quarter_code' => $qCode,
                    'expenses' => $expenses,
                    'budget' => $quarterBudget,
                    'budgeted_percentage' => round($budgetedPercentage, 2) . '%',
                ];

                $categoryTotalExpenses += $expenses;
            }

            // Calculate category totals
            $budgetedPercentageTotal = $categoryBudget > 0 ? ($categoryTotalExpenses / $categoryBudget) * 100 : 0;
            $categoryImplementation = $totalProjectBudget > 0 ? ($categoryTotalExpenses / $totalProjectBudget) * 100 : 0;

            $report[] = [
                'category' => $category->name,
                'category_id' => $category->id,
                'total_budget' => $categoryBudget,
                'total_expenses' => $categoryTotalExpenses,
                'budgeted_percentage' => round($budgetedPercentageTotal, 2) . '%',
                'total_budget_all' => $totalProjectBudget,
                'category_implementation' => round($categoryImplementation, 2) . '%',
                'quarters' => $categoryQuarters,
            ];
        }

        // Get asOfDate based on selected quarter
        $asOfDate = null;
        if (!$showAllQuarters && $selectedQuarter) {
            $quarter = Quarter::where('code', $selectedQuarter)->first();
            if ($quarter) {
                $endDate = \Carbon\Carbon::parse($quarter->end_date);
                $asOfDate = $endDate->format('d F Y'); // e.g., "30 September 2025"
            }
        } else {
            // Default to end of current fiscal year
            $asOfDate = '30 June 2026';
        }

        return [
            'report' => $report,
            'quarters' => $quarters->pluck('name', 'code')->toArray(),
            'showAllQuarters' => $showAllQuarters,
            'asOfDate' => $asOfDate,
        ];
    }

    /**
     * Get expenses for a specific project and time period
     */
    protected function getExpensesForPeriod($projectId, $months, $divisionId, $districtId)
    {
        return VoucherEntry::whereHas('voucher', function ($q) use ($projectId, $divisionId, $districtId, $months) {
            $q->where('project_id', $projectId)
              ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
              ->when($districtId, fn($q) => $q->where('district_id', $districtId))
              ->whereIn(DB::raw('MONTH(date)'), $months);
        })->sum('amount');
    }

    /**
     * Get category expenses for a specific time period
     */
    protected function getCategoryExpensesForPeriod($categoryId, $months, $projectId, $divisionId, $districtId)
    {
        return VoucherEntry::where('category_id', $categoryId)
            ->whereHas('voucher', function ($q) use ($projectId, $divisionId, $districtId, $months) {
                $q->when($projectId, fn($q) => $q->where('project_id', $projectId))
                  ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
                  ->when($districtId, fn($q) => $q->where('district_id', $districtId))
                  ->whereIn(DB::raw('MONTH(date)'), $months);
            })->sum('amount');
    }
}