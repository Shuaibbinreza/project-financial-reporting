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
use App\Models\Quarter;
use App\Services\FinancialReportService;
use Illuminate\Support\Facades\DB;

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
                          ->whereIn(DB::raw('MONTH(date)'), $months);
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
        $quarters = Quarter::orderBy('quarter_number')->get();

        return view('reports.financial', compact(
            'report', 'projects', 'categories', 'economicCodes', 'divisions', 'districts', 'quarters'
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
        // Optional filters
        $projectId = $request->project_id;
        $divisionIds = $request->division_ids;
        $districtIds = $request->district_ids;
        $selectedQuarter = $request->quarter;

        // If "All" is selected or no quarter, default to Q4 (latest)
        if (!$selectedQuarter || $selectedQuarter === 'all') {
            $selectedQuarter = 'Q4';
        }

        // Cumulative quarter months mapping (fiscal year starts July)
        // Q1 = July to September (months 7,8,9)
        // Q2 = July to December (months 7,8,9,10,11,12)
        // Q3 = July to March (months 7,8,9,10,11,12,1,2,3)
        // Q4 = July to June (months 7,8,9,10,11,12,1,2,3,4,5,6)
        $cumulativeMonths = [
            'Q1' => [7, 8, 9],
            'Q2' => [7, 8, 9, 10, 11, 12],
            'Q3' => [7, 8, 9, 10, 11, 12, 1, 2, 3],
            'Q4' => [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6],
        ];

        $months = $cumulativeMonths[$selectedQuarter] ?? $cumulativeMonths['Q4'];

        // Get all categories and projects
        $categories = \App\Models\Category::all();
        $projects = \App\Models\Project::all();

        $report = [];

        // Total project budget (filtered by project if selected)
        $totalProjectBudgetQuery = \App\Models\YearlyBudget::query()
            ->when($projectId, fn($q) => $q->where('project_id', $projectId));

        $totalProjectBudget = $totalProjectBudgetQuery->sum('total_amount');

        foreach ($categories as $category) {

            // Get expenses for selected quarter (cumulative from fiscal year start)
            $expenses = \App\Models\VoucherEntry::where('category_id', $category->id)
                ->whereHas('voucher', function ($q) use ($projectId, $divisionIds, $districtIds, $months) {
                    $q->when($projectId, fn($q) => $q->where('project_id', $projectId))
                      ->when($divisionIds, fn($q) => $q->whereIn('division_id', $divisionIds))
                      ->when($districtIds, fn($q) => $q->whereIn('district_id', $districtIds))
                      ->whereIn(DB::raw('MONTH(date)'), $months);
                })
                ->sum('amount');

            // Budget for this category for the selected quarter period
            // Calculate based on number of months in the cumulative period
            $monthCount = count($months);
            $categoryBudget = \App\Models\YearlyBudget::where('category_id', $category->id)
                ->when($projectId, fn($q) => $q->where('project_id', $projectId))
                ->sum('total_amount') * ($monthCount / 12);

            $budgetedPercentage = $categoryBudget > 0 ? ($expenses / $categoryBudget) * 100 : 0;

            // Total budget for this category (full year)
            $categoryBudgetTotal = \App\Models\YearlyBudget::where('category_id', $category->id)
                ->when($projectId, fn($q) => $q->where('project_id', $projectId))
                ->sum('total_amount');

            $budgetedPercentageTotal = $categoryBudgetTotal > 0 ? ($expenses / $categoryBudgetTotal) * 100 : 0;
            $projectImplementation = $totalProjectBudget > 0 ? ($expenses / $totalProjectBudget) * 100 : 0;

            $report[] = [
                'category' => $category->name,
                'expenses' => $expenses,
                'budget' => $categoryBudget,
                'budgeted_percentage' => round($budgetedPercentage, 2) . '%',
                'total_expenses' => $expenses,
                'total_budget' => $categoryBudgetTotal,
                'budgeted_percentage_total' => round($budgetedPercentageTotal, 2) . '%',
                'total_project_budget' => $totalProjectBudget,
                'project_implementation' => round($projectImplementation, 2) . '%',
            ];
        }

        $quarters = Quarter::orderBy('quarter_number')->get();

        return view('reports.category_summary', compact('report', 'projects', 'selectedQuarter', 'quarters'));
    }

    /**
     * AJAX: Get districts by division ID
     */
    public function getDistricts(Request $request)
    {
        $divisionId = $request->division_id;
        $districts = District::where('division_id', $divisionId)->orderBy('name')->get();
        return response()->json($districts);
    }

    /**
     * AJAX: Get filtered category summary data
     */
    public function categorySummaryAjax(Request $request)
    {
        $projectId = $request->project_id;
        $divisionId = $request->division_id;
        $districtId = $request->district_id;
        $selectedQuarter = $request->quarter;

        $service = new FinancialReportService();
        $data = $service->getCategorySummary($projectId, $divisionId, $districtId, $selectedQuarter);

        return response()->json($data);
    }

    /**
     * Project-wise summary report (main view)
     */
    public function projectSummary(Request $request)
    {
        $projects = Project::all();
        $divisions = Division::all();
        $districts = District::all();
        $quarters = Quarter::orderBy('quarter_number')->get();

        return view('reports.project_summary', compact('projects', 'divisions', 'districts', 'quarters'));
    }

    /**
     * AJAX: Get filtered project-wise summary data
     */
    public function projectSummaryAjax(Request $request)
    {
        $divisionId = $request->division_id;
        $districtId = $request->district_id;
        $selectedQuarter = $request->quarter;

        $service = new FinancialReportService();
        $data = $service->getProjectSummary($divisionId, $districtId, $selectedQuarter);

        return response()->json($data);
    }

    /**
     * Project Spendings - Shows all spendings under each project
     */
    public function projectSpendings(Request $request)
    {
        $projects = Project::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $economicCodes = EconomicCode::orderBy('code')->get();

        return view('reports.project_spendings', [
            'title' => 'Project Spendings',
            'breadcrumb' => 'Project Spendings',
            'icon' => 'bi bi-cash-stack',
            'description' => 'View all spendings under each project',
            'projects' => $projects,
            'divisions' => $divisions,
            'categories' => $categories,
            'economicCodes' => $economicCodes
        ]);
    }

    /**
     * AJAX: Get project spendings data
     */
    public function projectSpendingsAjax(Request $request)
    {
        $projectId = $request->project_id;
        $divisionId = $request->division_id;
        $categoryId = $request->category_id;
        $economicCodeId = $request->economic_code_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $query = VoucherEntry::with(['voucher.project', 'voucher.division', 'category', 'economicCode'])
            ->select('voucher_entries.*');

        if ($projectId) {
            $query->whereHas('voucher', function ($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }

        if ($divisionId) {
            $query->whereHas('voucher', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($economicCodeId) {
            $query->where('economic_code_id', $economicCodeId);
        }

        if ($dateFrom) {
            $query->whereHas('voucher', function ($q) use ($dateFrom) {
                $q->where('date', '>=', $dateFrom);
            });
        }

        if ($dateTo) {
            $query->whereHas('voucher', function ($q) use ($dateTo) {
                $q->where('date', '<=', $dateTo);
            });
        }

        $entries = $query->orderBy('id', 'desc')->get();

        $data = $entries->map(function ($entry) {
            $voucher = $entry->voucher;
            return [
                'id' => $entry->id,
                'voucher_id' => $voucher ? $voucher->id : null,
                'voucher_no' => $voucher ? '#' . str_pad($voucher->id, 6, '0', STR_PAD_LEFT) : '-',
                'voucher_date' => $voucher && $voucher->date ? \Carbon\Carbon::parse($voucher->date)->format('d M Y') : '-',
                'project_name' => $voucher && $voucher->project ? $voucher->project->name : '-',
                'division_name' => $voucher && $voucher->division ? $voucher->division->name : '-',
                'category_name' => $entry->category ? $entry->category->name : '-',
                'economic_code' => $entry->economicCode ? $entry->economicCode->code : '-',
                'amount' => number_format($entry->amount, 2),
                'amount_raw' => $entry->amount
            ];
        });

        // Calculate totals per project
        $projectTotals = [];
        foreach ($data as $item) {
            $projectName = $item['project_name'];
            if (!isset($projectTotals[$projectName])) {
                $projectTotals[$projectName] = 0;
            }
            $projectTotals[$projectName] += $item['amount_raw'];
        }

        return response()->json([
            'data' => $data,
            'projectTotals' => $projectTotals
        ]);
    }

    /**
     * Dashboard - Shows overview statistics
     */
    public function dashboard()
    {
        $stats = [
            'total_projects' => Project::count(),
            'total_budget' => YearlyBudget::sum('total_amount'),
            'total_expenses' => VoucherEntry::sum('amount'),
            'total_districts' => District::count(),
        ];

        return view('welcome', compact('stats'));
    }
}
