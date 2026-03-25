@extends('layouts.report')

@section('title', 'Category Summary')
@section('breadcrumb', 'Category Summary')
@section('icon', 'bi bi-pie-chart')
@section('description', 'View category-wise financial summary with budget vs expense analysis')

@section('filters')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Filters
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="project_id" class="form-label">Project</label>
                <select name="project_id" id="project_id" class="form-select">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="division_id" class="form-label">Division</label>
                <select name="division_id" id="division_id" class="form-select">
                    <option value="">All Divisions</option>
                    @foreach(\App\Models\Division::all() as $division)
                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="district_id" class="form-label">District</label>
                <select name="district_id" id="district_id" class="form-select" disabled>
                    <option value="">All Districts</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="quarter" class="form-label">Quarter</label>
                <select name="quarter" id="quarter" class="form-select">
                    <option value="all">All Quarters</option>
                    @foreach($quarters as $quarter)
                        <option value="{{ $quarter->code }}" {{ $loop->last ? 'selected' : '' }}>{{ $quarter->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <button type="button" class="btn btn-primary" id="filterBtn">
                    <i class="bi bi-filter me-1"></i>Filter
                </button>
                <button type="button" class="btn btn-outline-secondary" id="clearBtn">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('report-content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-table me-2"></i>Category Summary - <span id="quarterLabel">Q4</span>
        </h5>
        <span class="badge bg-secondary" id="categoryCount">0 Categories</span>
    </div>
    <div class="card-body p-0">
        <div id="reportTableContainer">
            <!-- Table will be loaded here via AJAX -->
        </div>
        <div id="loadingSpinner" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading data...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Get districts by division (cascading dropdown)
    document.getElementById('division_id').addEventListener('change', function() {
        const divisionId = this.value;
        const districtSelect = document.getElementById('district_id');

        // Clear existing options
        districtSelect.innerHTML = '<option value="">All Districts</option>';

        if (divisionId) {
            districtSelect.disabled = true;
            fetch(`/reports/get-districts?division_id=${divisionId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                    districtSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching districts:', error);
                    districtSelect.disabled = false;
                });
        } else {
            districtSelect.disabled = true;
        }
    });

    // Quarter descriptions
    const quarterDescriptions = {
        @foreach($quarters as $quarter)
        '{{ $quarter->code }}': 'Showing data for {{ $quarter->name }}',
        @endforeach
        'All': 'Showing total summation of all quarters'
    };

    // Fetch report data via AJAX
    function fetchReport() {
        const projectId = document.getElementById('project_id').value;
        const divisionId = document.getElementById('division_id').value;
        const districtId = document.getElementById('district_id').value;
        const quarter = document.getElementById('quarter').value;

        // Show loading spinner
        document.getElementById('reportTableContainer').innerHTML = '';
        document.getElementById('loadingSpinner').style.display = 'block';

        // Build query string
        const params = new URLSearchParams();
        if (projectId) params.append('project_id', projectId);
        if (divisionId) params.append('division_id', divisionId);
        if (districtId) params.append('district_id', districtId);
        if (quarter) params.append('quarter', quarter);

        fetch(`/reports/category-summary/ajax?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingSpinner').style.display = 'none';
                renderTable(data);
                updateHeader(data);
            })
            .catch(error => {
                console.error('Error fetching report:', error);
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('reportTableContainer').innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                        <p class="text-danger mt-3">Error loading data. Please try again.</p>
                    </div>
                `;
            });
    }

    function updateHeader(data) {
        const quarter = data.selectedQuarter;
        document.getElementById('quarterLabel').textContent = quarter;
        document.getElementById('categoryCount').textContent = `${data.report.length} Categories`;
    }

    function renderTable(data) {
        const container = document.getElementById('reportTableContainer');

        if (data.report.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">No data available for the selected filters</p>
                </div>
            `;
            return;
        }

        let thead = '';
        let tbody = '';

        // Get quarter info for display
        const quarterName = data.quarterName || 'All Quarters';
        const asOfDate = data.asOfDate || '';
        
        // Split asOfDate into day, month, year for better display
        const dateParts = asOfDate.split(' ');
        const day = dateParts[0] || '';
        const month = dateParts[1] || '';
        const year = dateParts[2] || '';
        
        // Simple table with quarter-specific columns (using <br> for multi-line headers)
        thead = `
            <thead class="table-dark">
                <tr>
                    <th class="align-middle" style="min-width: 150px;">Cost Category</th>
                    <th class="align-middle text-end">Expenses<br>as of<br>${day} ${month}<br>${year}</th>
                    <th class="align-middle text-end">Budget<br>as of<br>${day} ${month}<br>${year}</th>
                    <th class="align-middle text-center">Budgeted<br>as of<br>${day} ${month}<br>${year} (%)</th>
                    <th class="align-middle text-end">Total<br>Project<br>Budget</th>
                    <th class="align-middle text-center">Implementation<br>as of<br>${day} ${month}<br>${year} (%)</th>
                </tr>
            </thead>
        `;

        tbody = data.report.map(row => {
            const pct = parseFloat(row.budgeted_percentage);
            const implPct = parseFloat(row.category_implementation);

            return `
                <tr>
                    <td class="fw-bold">${row.category}</td>
                    <td class="text-end">${numberFormat(row.total_expenses)}</td>
                    <td class="text-end">${numberFormat(row.total_budget)}</td>
                    <td class="text-center">
                        <span class="badge ${pct >= 100 ? 'bg-danger' : (pct >= 75 ? 'bg-warning' : 'bg-success')}">
                            ${row.budgeted_percentage}
                        </span>
                    </td>
                    <td class="text-end">${numberFormat(row.total_budget_all)}</td>
                    <td class="text-center">
                        <span class="badge ${implPct >= 100 ? 'bg-danger' : (implPct >= 75 ? 'bg-warning' : 'bg-success')}">
                            ${row.category_implementation}
                        </span>
                    </td>
                </tr>
            `;
        }).join('');

        container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-primary table-striped mb-0 report-table" id="categorySummaryTable">
                    ${thead}
                    <tbody>${tbody}</tbody>
                </table>
            </div>
        `;
        
        // Initialize DataTables after table is rendered
        initDataTable();
    }

    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#categorySummaryTable')) {
            $('#categorySummaryTable').DataTable().destroy();
        }
        
        $('#categorySummaryTable').DataTable(window.DataTableCommonOptions);
    }

    function numberFormat(num) {
        return new Intl.NumberFormat('en-US').format(num);
    }

    // Event listeners
    document.getElementById('filterBtn').addEventListener('click', fetchReport);

    document.getElementById('clearBtn').addEventListener('click', function() {
        document.getElementById('project_id').value = '';
        document.getElementById('division_id').value = '';
        document.getElementById('district_id').innerHTML = '<option value="">All Districts</option>';
        document.getElementById('district_id').disabled = true;
        document.getElementById('quarter').value = 'all';
        fetchReport();
    });

    // Load data on page load
    document.addEventListener('DOMContentLoaded', fetchReport);
</script>
@endsection
