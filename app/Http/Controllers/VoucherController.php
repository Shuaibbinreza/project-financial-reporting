<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\Project;
use App\Models\Division;
use App\Models\Category;
use App\Models\EconomicCode;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    /**
     * Display a listing of the vouchers with entries.
     */
    public function index(Request $request)
    {
        $projects = Project::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $economicCodes = EconomicCode::orderBy('code')->get();

        return view('vouchers.index', compact('projects', 'divisions', 'categories', 'economicCodes'));
    }

    /**
     * Get vouchers data for DataTables.
     */
    public function data(Request $request)
    {
        $query = Voucher::with(['project', 'division', 'entries.category', 'entries.economicCode'])
            ->select('vouchers.*');

        // Apply filters
        if ($request->has('project_id') && $request->project_id) {
            $projectIds = is_array($request->project_id) ? $request->project_id : [$request->project_id];
            $query->whereIn('project_id', $projectIds);
        }

        if ($request->has('division_id') && $request->division_id) {
            $divisionIds = is_array($request->division_id) ? $request->division_id : [$request->division_id];
            $query->whereIn('division_id', $divisionIds);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $vouchers = $query->orderBy('date', 'desc')->get();

        $data = $vouchers->map(function ($voucher) {
            $totalAmount = $voucher->entries->sum('amount');
            $entriesDetail = $voucher->entries->map(function ($entry) {
                return [
                    'economic_code' => $entry->economicCode ? $entry->economicCode->code : 'N/A',
                    'category' => $entry->category ? $entry->category->name : 'N/A',
                    'amount' => number_format($entry->amount, 2)
                ];
            });

            return [
                'id' => $voucher->id,
                'project_name' => $voucher->project ? $voucher->project->name : '-',
                'division_name' => $voucher->division ? $voucher->division->name : '-',
                'voucher_date' => $voucher->date ? \Carbon\Carbon::parse($voucher->date)->format('d M Y') : '-',
                'total_amount' => number_format($totalAmount, 2),
                'entries_count' => $voucher->entries->count(),
                'entries' => $entriesDetail
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Get voucher entries data for DataTables.
     */
    public function entriesData(Request $request)
    {
        $query = VoucherEntry::with(['voucher.project', 'voucher.division', 'category', 'economicCode'])
            ->select('voucher_entries.*');

        // Apply filters
        if ($request->has('project_id') && $request->project_id) {
            $projectIds = is_array($request->project_id) ? $request->project_id : [$request->project_id];
            $query->whereHas('voucher', function ($q) use ($projectIds) {
                $q->whereIn('project_id', $projectIds);
            });
        }

        if ($request->has('division_id') && $request->division_id) {
            $divisionIds = is_array($request->division_id) ? $request->division_id : [$request->division_id];
            $query->whereHas('voucher', function ($q) use ($divisionIds) {
                $q->whereIn('division_id', $divisionIds);
            });
        }

        if ($request->has('category_id') && $request->category_id) {
            $categoryIds = is_array($request->category_id) ? $request->category_id : [$request->category_id];
            $query->whereIn('category_id', $categoryIds);
        }

        if ($request->has('economic_code_id') && $request->economic_code_id) {
            $economicCodeIds = is_array($request->economic_code_id) ? $request->economic_code_id : [$request->economic_code_id];
            $query->whereIn('economic_code_id', $economicCodeIds);
        }

        $entries = $query->orderBy('id', 'desc')->get();

        $data = $entries->map(function ($entry) {
            return [
                'id' => $entry->id,
                'voucher_date' => $entry->voucher && $entry->voucher->date
                    ? \Carbon\Carbon::parse($entry->voucher->date)->format('d M Y')
                    : '-',
                'voucher_no' => $entry->voucher ? '#' . str_pad($entry->voucher->id, 6, '0', STR_PAD_LEFT) : '-',
                'project_name' => $entry->voucher && $entry->voucher->project ? $entry->voucher->project->name : '-',
                'division_name' => $entry->voucher && $entry->voucher->division ? $entry->voucher->division->name : '-',
                'category_name' => $entry->category ? $entry->category->name : '-',
                'economic_code' => $entry->economicCode ? $entry->economicCode->code : '-',
                'amount' => number_format($entry->amount, 2)
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Display a listing of voucher entries only.
     */
    public function entries(Request $request)
    {
        $projects = Project::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $economicCodes = EconomicCode::orderBy('code')->get();

        return view('vouchers.entries', compact('projects', 'divisions', 'categories', 'economicCodes'));
    }
}
