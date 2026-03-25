@extends('layouts.report')

@section('title', 'Project Spendings')
@section('breadcrumb', 'Project Spendings')
@section('icon', 'bi bi-cash-stack')
@section('description', 'View all spendings under each project')

@section('filters')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Filters
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.project-spendings') }}" class="row g-3">
            <div class="col-md-2">
                <label for="project_id" class="form-label">Project</label>
                <select name="project_id[]" id="filter_project_id" class="form-select select2-multiple" multiple>
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ in_array($project->id, request('project_id', [])) ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="division_id" class="form-label">Division</label>
                <select name="division_id[]" id="filter_division_id" class="form-select select2-multiple" multiple>
                    <option value="">All Divisions</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ in_array($division->id, request('division_id', [])) ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id[]" id="filter_category_id" class="form-select select2-multiple" multiple>
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ in_array($category->id, request('category_id', [])) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="economic_code_id" class="form-label">Economic Code</label>
                <select name="economic_code_id[]" id="filter_economic_code_id" class="form-select select2-multiple" multiple>
                    <option value="">All Economic Codes</option>
                    @foreach($economicCodes as $ec)
                        <option value="{{ $ec->id }}" {{ in_array($ec->id, request('economic_code_id', [])) ? 'selected' : '' }}>
                            {{ $ec->code }} - {{ $ec->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" name="date_from" id="filter_date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" name="date_to" id="filter_date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-12 d-flex align-items-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('reports.project-spendings') }}" class="btn btn-outline-secondary">
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
            <i class="bi bi-table me-2"></i>Spending Details
        </h5>
        <div>
            <span class="badge bg-primary me-2">Total: <span id="totalAmount">৳ 0.00</span></span>
            <span class="badge bg-success">Projects: <span id="projectCount">0</span></span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0" id="spendingsTable" style="width: 100%">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Voucher No</th>
                        <th>Date</th>
                        <th>Project</th>
                        <th>Division</th>
                        <th>Category</th>
                        <th>Economic Code</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Summary by Project Modal -->
<div class="modal fade" id="projectSummaryModal" tabindex="-1" aria-labelledby="projectSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectSummaryModalLabel">Project Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="projectSummaryTable">
                    <thead class="table-secondary">
                        <tr>
                            <th>Project</th>
                            <th class="text-end">Total Spending</th>
                        </tr>
                    </thead>
                    <tbody id="projectSummaryBody">
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>Grand Total</td>
                            <td class="text-end" id="grandTotal">৳ 0.00</td>
                        </tr>
                    </tfoot>
                </table>
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
let spendingsTable;
let projectTotals = {};

$(document).ready(function() {
    // Initialize Select2 for multiple select
    $('.select2-multiple').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select options',
        allowClear: true
    });

    initSpendingsTable();
});

function getFilterParams() {
    return {
        project_id: $('#filter_project_id').val(),
        division_id: $('#filter_division_id').val(),
        category_id: $('#filter_category_id').val(),
        economic_code_id: $('#filter_economic_code_id').val(),
        date_from: $('#filter_date_from').val(),
        date_to: $('#filter_date_to').val()
    };
}

function initSpendingsTable() {
    spendingsTable = $('#spendingsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("reports.project-spendings-ajax") }}',
            data: function(d) {
                return $.extend({}, d, getFilterParams());
            },
            dataSrc: function(json) {
                // Update project totals
                projectTotals = json.projectTotals || {};
                updateSummary(json.data);
                return json.data;
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'voucher_no', name: 'voucher_no' },
            { data: 'voucher_date', name: 'voucher_date' },
            { data: 'project_name', name: 'project_name' },
            { data: 'division_name', name: 'division_name' },
            { data: 'category_name', name: 'category_name' },
            { data: 'economic_code', name: 'economic_code' },
            { data: 'amount', name: 'amount', className: 'text-end' }
        ],
        order: [[2, 'desc']],
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'csv',
                className: 'btn btn-info btn-sm'
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm'
            },
            {
                extend: 'print',
                className: 'btn btn-warning btn-sm'
            },
            {
                text: '<i class="bi bi-pie-chart"></i> Summary',
                className: 'btn btn-primary btn-sm',
                action: function() {
                    showProjectSummary();
                }
            }
        ],
        language: {
            emptyTable: 'No spending data found'
        }
    });
}

function applyFilters() {
    spendingsTable.ajax.reload();
}

function updateSummary(data) {
    let total = 0;
    let projectCount = 0;

    // Calculate totals
    for (let key in projectTotals) {
        total += projectTotals[key];
        projectCount++;
    }

    $('#totalAmount').text('৳ ' + numberFormat(total));
    $('#projectCount').text(projectCount);
}

function numberFormat(num) {
    return new Intl.NumberFormat('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
}

function showProjectSummary() {
    let html = '';
    let grandTotal = 0;

    for (let projectName in projectTotals) {
        html += '<tr>';
        html += '<td>' + projectName + '</td>';
        html += '<td class="text-end">৳ ' + numberFormat(projectTotals[projectName]) + '</td>';
        html += '</tr>';
        grandTotal += projectTotals[projectName];
    }

    if (html === '') {
        html = '<tr><td colspan="2" class="text-center text-muted">No data available</td></tr>';
    }

    $('#projectSummaryBody').html(html);
    $('#grandTotal').text('৳ ' + numberFormat(grandTotal));
    $('#projectSummaryModal').modal('show');
}
</script>
@endsection
