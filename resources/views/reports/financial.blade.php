@extends('layouts.report')

@section('title', 'Financial Reports')
@section('breadcrumb', 'Financial Reports')
@section('icon', 'bi bi-graph-up-arrow')
@section('description', 'View and filter project financial data by various criteria')

@section('filters')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Filters
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.financial') }}" class="row g-3">
            <div class="col-md-2">
                <label for="project_id" class="form-label">Project</label>
                <select name="project_id" id="project_id" class="form-select">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="division_id" class="form-label">Division</label>
                <select name="division_id" id="division_id" class="form-select">
                    <option value="">All Divisions</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="district_id" class="form-label">District</label>
                <select name="district_id" id="district_id" class="form-select">
                    <option value="">All Districts</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="economic_code_id" class="form-label">Economic Code</label>
                <select name="economic_code_id" id="economic_code_id" class="form-select">
                    <option value="">All Codes</option>
                    @foreach($economicCodes as $code)
                        <option value="{{ $code->id }}" {{ request('economic_code_id') == $code->id ? 'selected' : '' }}>
                            {{ $code->code }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label for="quarter" class="form-label">Quarter</label>
                <select name="quarter" id="quarter" class="form-select">
                    <option value="">All Quarters</option>
                    @foreach($quarters as $quarter)
                        <option value="{{ $quarter->code }}" {{ request('quarter') == $quarter->code ? 'selected' : '' }}>{{ $quarter->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8 d-flex align-items-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('reports.financial') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('report-content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-cash-stack me-2"></i>All Spendings
        </h5>
        <div>
            <span class="badge bg-primary me-2" id="totalEntries">0 Entries</span>
            <span class="badge bg-success" id="totalAmount">Total: ৳ 0</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 report-table" id="financialTable" style="width: 100%">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Voucher No</th>
                        <th>Project</th>
                        <th>Division</th>
                        <th>District</th>
                        <th>Category</th>
                        <th>Economic Code</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Spending Details Modal -->
<div class="modal fade" id="spendingDetailModal" tabindex="-1" aria-labelledby="spendingDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="spendingDetailModalLabel">Spending Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="spendingDetailContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let financialTable;

$(document).ready(function() {
    // Destroy existing DataTable if it exists to prevent reinitialization error
    if ($.fn.DataTable.isDataTable('#financialTable')) {
        $('#financialTable').DataTable().destroy();
    }
    initFinancialTable();
});

function getFilterParams() {
    return {
        project_id: $('#project_id').val(),
        division_id: $('#division_id').val(),
        district_id: $('#district_id').val(),
        category_id: $('#category_id').val(),
        economic_code_id: $('#economic_code_id').val(),
        date_from: $('#date_from').val(),
        date_to: $('#date_to').val(),
        quarter: $('#quarter').val()
    };
}

function initFinancialTable() {
    financialTable = $('#financialTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("reports.financial-data") }}',
            data: function(d) {
                return $.extend({}, d, getFilterParams());
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'voucher_date', name: 'voucher_date' },
            { data: 'voucher_no', name: 'voucher_no' },
            { data: 'project_name', name: 'project_name' },
            { data: 'division_name', name: 'division_name' },
            { data: 'district_name', name: 'district_name' },
            { data: 'category_name', name: 'category_name' },
            { data: 'economic_code', name: 'economic_code' },
            { data: 'amount', name: 'amount', className: 'text-end' },
            {
                data: 'id',
                name: 'actions',
                className: 'text-center',
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-outline-primary" onclick="showSpendingDetails(' + data + ')">' +
                           '<i class="bi bi-eye"></i> View</button>';
                }
            }
        ],
        order: [[1, 'desc']],
        dom: 'Blfrtip',
        buttons: window.DataTableCommonOptions.buttons,
        language: window.DataTableCommonOptions.language,
        initComplete: window.DataTableCommonOptions.initComplete,
    });

    // Update counts after table is loaded
    financialTable.on('xhr.dt', function(e, settings, json) {
        if (json && json.data) {
            $('#totalEntries').text(json.data.length + ' Entries');

            // Calculate total
            let total = 0;
            json.data.forEach(function(item) {
                total += parseFloat(item.amount.replace(/,/g, ''));
            });
            $('#totalAmount').text('Total: ৳ ' + total.toLocaleString('en-BD', {minimumFractionDigits: 2}));
        }
    });
}

function applyFilters() {
    financialTable.ajax.reload();
}

function showSpendingDetails(entryId) {
    // Get entry details from table data
    let tableData = financialTable.data();
    let entry = null;

    for (let i = 0; i < tableData.length; i++) {
        if (tableData[i].id === entryId) {
            entry = tableData[i];
            break;
        }
    }

    if (!entry) {
        alert('Entry not found');
        return;
    }

    let html = '<div class="row mb-3">';
    html += '<div class="col-md-4"><strong>Entry ID:</strong> ' + entry.id + '</div>';
    html += '<div class="col-md-4"><strong>Voucher No:</strong> ' + entry.voucher_no + '</div>';
    html += '<div class="col-md-4"><strong>Date:</strong> ' + entry.voucher_date + '</div>';
    html += '</div>';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-4"><strong>Project:</strong> ' + entry.project_name + '</div>';
    html += '<div class="col-md-4"><strong>Division:</strong> ' + entry.division_name + '</div>';
    html += '<div class="col-md-4"><strong>District:</strong> ' + entry.district_name + '</div>';
    html += '</div>';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-4"><strong>Category:</strong> ' + entry.category_name + '</div>';
    html += '<div class="col-md-4"><strong>Economic Code:</strong> <span class="badge bg-secondary">' + entry.economic_code + '</span></div>';
    html += '<div class="col-md-4"><strong>Amount:</strong> <span class="text-success fw-bold">৳ ' + entry.amount + '</span></div>';
    html += '</div>';

    $('#spendingDetailContent').html(html);
    $('#spendingDetailModal').modal('show');
}
</script>
@endsection
