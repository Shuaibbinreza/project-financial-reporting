@extends('layouts.report')

@section('title', 'Vouchers')
@section('breadcrumb', 'Voucher Management')
@section('icon', 'bi bi-receipt')
@section('description', 'View and manage vouchers and their entries')

@section('filters')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Filters
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('vouchers.index') }}" class="row g-3">
            <div class="col-md-3">
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
            <div class="col-md-3">
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
            <div class="col-md-3">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" name="date_from" id="filter_date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" name="date_to" id="filter_date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-12 d-flex align-items-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('vouchers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('report-content')
<!-- Nav Tabs -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" id="voucherTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="vouchers-tab" data-bs-toggle="tab" data-bs-target="#vouchers" type="button" role="tab">
                    <i class="bi bi-receipt me-1"></i>Vouchers
                    <span class="badge bg-secondary ms-1" id="voucherCount">0</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="entries-tab" data-bs-toggle="tab" data-bs-target="#entries" type="button" role="tab">
                    <i class="bi bi-list-ul me-1"></i>Voucher Entries
                    <span class="badge bg-secondary ms-1" id="entryCount">0</span>
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="voucherTabsContent">
            <!-- Vouchers Tab -->
            <div class="tab-pane fade show active" id="vouchers" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="vouchersTable" style="width: 100%">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Division</th>
                                <th class="text-end">Total Amount</th>
                                <th>Entries</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Voucher Entries Tab -->
            <div class="tab-pane fade" id="entries" role="tabpanel">
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
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Voucher Details Modal -->
<div class="modal fade" id="voucherDetailModal" tabindex="-1" aria-labelledby="voucherDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="voucherDetailModalLabel">Voucher Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="voucherDetailContent">
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
let vouchersTable;
let voucherEntriesTable;

$(document).ready(function() {
    initVouchersTable();
    initVoucherEntriesTable();
});

function getFilterParams() {
    return {
        project_id: $('#filter_project_id').val(),
        division_id: $('#filter_division_id').val(),
        date_from: $('#filter_date_from').val(),
        date_to: $('#filter_date_to').val()
    };
}

function initVouchersTable() {
    vouchersTable = $('#vouchersTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("vouchers.data") }}',
            data: function(d) {
                return $.extend({}, d, getFilterParams());
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'voucher_date', name: 'voucher_date' },
            { data: 'project_name', name: 'project_name' },
            { data: 'division_name', name: 'division_name' },
            { data: 'total_amount', name: 'total_amount', className: 'text-end' },
            { data: 'entries_count', name: 'entries_count' },
            { 
                data: 'entries', 
                name: 'entries',
                render: function(data, type, row) {
                    if (data && data.length > 0) {
                        let html = '<button class="btn btn-sm btn-outline-primary" onclick="showVoucherDetails(' + row.id + ')">';
                        html += '<i class="bi bi-eye"></i> ' + data.length + ' entries';
                        html += '</button>';
                        return html;
                    }
                    return '<span class="text-muted">No entries</span>';
                }
            }
        ],
        order: [[1, 'desc']],
        dom: 'Blfrtip',
        buttons: [
            'copy', 'excel', 'csv', 'pdf', 'print'
        ],
        language: {
            emptyTable: 'No vouchers found'
        }
    });

    // Update count after table is loaded
    vouchersTable.on('xhr.dt', function(e, settings, json) {
        $('#voucherCount').text(json.data.length);
    });
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
            { data: 'amount', name: 'amount', className: 'text-end' }
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
        $('#entryCount').text(json.data.length);
    });
}

function applyFilters() {
    vouchersTable.ajax.reload();
    voucherEntriesTable.ajax.reload();
}

function showVoucherDetails(voucherId) {
    // Get voucher details from table data
    let tableData = vouchersTable.data();
    let voucher = null;
    
    for (let i = 0; i < tableData.length; i++) {
        if (tableData[i].id === voucherId) {
            voucher = tableData[i];
            break;
        }
    }

    if (!voucher) {
        alert('Voucher not found');
        return;
    }

    let html = '<div class="row mb-3">';
    html += '<div class="col-md-6"><strong>Date:</strong> ' + voucher.voucher_date + '</div>';
    html += '<div class="col-md-6"><strong>Project:</strong> ' + voucher.project_name + '</div>';
    html += '</div>';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-6"><strong>Division:</strong> ' + voucher.division_name + '</div>';
    html += '<div class="col-md-6"><strong>Total Amount:</strong> ৳ ' + voucher.total_amount + '</div>';
    html += '</div>';
    
    html += '<h6>Entries</h6>';
    html += '<table class="table table-sm table-bordered">';
    html += '<thead><tr><th>Economic Code</th><th>Category</th><th class="text-end">Amount</th></tr></thead>';
    html += '<tbody>';
    
    if (voucher.entries && voucher.entries.length > 0) {
        voucher.entries.forEach(function(entry) {
            html += '<tr>';
            html += '<td><span class="badge bg-secondary">' + entry.economic_code + '</span></td>';
            html += '<td>' + entry.category + '</td>';
            html += '<td class="text-end">৳ ' + entry.amount + '</td>';
            html += '</tr>';
        });
    } else {
        html += '<tr><td colspan="3" class="text-center text-muted">No entries</td></tr>';
    }
    
    html += '</tbody></table>';

    $('#voucherDetailContent').html(html);
    $('#voucherDetailModal').modal('show');
}
</script>
@endsection