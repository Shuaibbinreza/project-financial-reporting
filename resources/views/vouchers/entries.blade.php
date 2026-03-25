@extends('layouts.report')

@section('title', 'Voucher Entries')
@section('breadcrumb', 'Voucher Entries Management')
@section('icon', 'bi bi-list-ul')
@section('description', 'View and manage all voucher entries')

@section('filters')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Filters
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('vouchers.entries') }}" class="row g-3">
            <div class="col-md-2">
                <label for="project_id" class="form-label">Project</label>
                <select name="project_id" id="filter_project_id" class="form-select">
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
                <select name="division_id" id="filter_division_id" class="form-select">
                    <option value="">All Divisions</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="filter_category_id" class="form-select">
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
                <select name="economic_code_id" id="filter_economic_code_id" class="form-select">
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
                    <a href="{{ route('vouchers.entries') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('report-content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>All Voucher Entries
            </h5>
            <div class="text-muted">
                <span id="totalEntries">0</span> entries found
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="voucherEntriesTable" style="width: 100%">
                <thead class="table-dark">
                    <tr>
                        <th>Entry ID</th>
                        <th>Voucher No</th>
                        <th>Date</th>
                        <th>Project</th>
                        <th>Division</th>
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

<!-- Entry Details Modal -->
<div class="modal fade" id="entryDetailModal" tabindex="-1" aria-labelledby="entryDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entryDetailModalLabel">Voucher Entry Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="entryDetailContent">
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
let voucherEntriesTable;

$(document).ready(function() {
    // Destroy existing DataTable if it exists to prevent reinitialization error
    if ($.fn.DataTable.isDataTable('#voucherEntriesTable')) {
        $('#voucherEntriesTable').DataTable().destroy();
    }
    initVoucherEntriesTable();
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

function initVoucherEntriesTable() {
    voucherEntriesTable = $('#voucherEntriesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("vouchers.entries-data") }}',
            data: function(d) {
                return $.extend({}, d, getFilterParams());
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
            { data: 'amount', name: 'amount', className: 'text-end' },
            {
                data: 'id',
                name: 'actions',
                className: 'text-center',
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-outline-primary" onclick="showEntryDetails(' + data + ')">' +
                           '<i class="bi bi-eye"></i> View</button>';
                }
            }
        ],
        order: [[0, 'desc']],
        dom: 'Blfrtip',
        buttons: [
            'copy', 'excel', 'csv', 'pdf', 'print'
        ],
        language: {
            emptyTable: 'No voucher entries found'
        }
    });

    // Update count after table is loaded
    voucherEntriesTable.on('xhr.dt', function(e, settings, json) {
        if (json && json.data) {
            $('#totalEntries').text(json.data.length);
        }
    });
}

function applyFilters() {
    voucherEntriesTable.ajax.reload();
}

function showEntryDetails(entryId) {
    // Get entry details from table data
    let tableData = voucherEntriesTable.data();
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
    html += '<div class="col-md-6"><strong>Entry ID:</strong> ' + entry.id + '</div>';
    html += '<div class="col-md-6"><strong>Voucher No:</strong> ' + entry.voucher_no + '</div>';
    html += '</div>';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-6"><strong>Date:</strong> ' + entry.voucher_date + '</div>';
    html += '<div class="col-md-6"><strong>Amount:</strong> ৳ ' + entry.amount + '</div>';
    html += '</div>';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-6"><strong>Project:</strong> ' + entry.project_name + '</div>';
    html += '<div class="col-md-6"><strong>Division:</strong> ' + entry.division_name + '</div>';
    html += '</div>';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-6"><strong>Category:</strong> ' + entry.category_name + '</div>';
    html += '<div class="col-md-6"><strong>Economic Code:</strong> <span class="badge bg-secondary">' + entry.economic_code + '</span></div>';
    html += '</div>';

    $('#entryDetailContent').html(html);
    $('#entryDetailModal').modal('show');
}
</script>
@endsection
