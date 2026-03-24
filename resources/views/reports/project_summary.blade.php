<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project-wise Financial Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-1">
                    <i class="bi bi-briefcase me-2"></i>Project-wise Financial Summary
                </h1>
                <p class="text-muted mb-0" id="headerDescription">
                    Showing data from July to June (Q4 - Full Year Cumulative)
                </p>
            </div>
            <div class="col-auto">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-house me-1"></i>Home
                </a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mb-3">
            <div class="btn-group" role="group">
                <a href="{{ route('reports.financial') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-bar-chart me-1"></i>Financial Report
                </a>
                <a href="{{ route('reports.cutoff') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-scissors me-1"></i>Cutoff Report
                </a>
                <a href="{{ route('reports.categorySummary') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pie-chart me-1"></i>Category Summary
                </a>
                <a href="{{ route('reports.projectSummary') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-briefcase me-1"></i>Project Summary
                </a>
            </div>
        </div>

        <!-- Filters Card -->
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
                            <option value="Q4" selected>Q4 (April - June)</option>
                            <option value="Q3">Q3 (January - March)</option>
                            <option value="Q2">Q2 (October - December)</option>
                            <option value="Q1">Q1 (July - September)</option>
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

        <!-- Report Table Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-table me-2"></i>Project Summary - <span id="quarterLabel">Q4</span>
                </h5>
                <span class="badge bg-secondary" id="projectCount">0 Projects</span>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
            'Q1': 'Showing data for Q1 (July - September)',
            'Q2': 'Showing data for Q2 (October - December)',
            'Q3': 'Showing data for Q3 (January - March)',
            'Q4': 'Showing data for Q4 (April - June)',
            'All': 'Showing total summation of all quarters (Q1 + Q2 + Q3 + Q4)'
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

            fetch(`/reports/project-summary/ajax?${params.toString()}`)
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
            document.getElementById('headerDescription').textContent = quarterDescriptions[quarter] || quarterDescriptions['Q4'];
            document.getElementById('quarterLabel').textContent = quarter;
            document.getElementById('projectCount').textContent = `${data.report.length} Projects`;
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

            if (data.showAllQuarters) {
                // Show summation of all quarters (single row format)
                thead = `
                    <thead class="table-dark">
                        <tr>
                            <th class="align-middle">Project Name</th>
                            <th class="align-middle text-end">Total Expenses (Q1-Q4)</th>
                            <th class="align-middle text-end">Total Budget</th>
                            <th class="align-middle text-center">Budgeted (%)</th>
                            <th class="align-middle text-end">Total Budget (All Projects)</th>
                            <th class="align-middle text-center">Implementation (%)</th>
                        </tr>
                    </thead>
                `;

                tbody = data.report.map(row => {
                    const pct = parseFloat(row.budgeted_percentage);
                    const implPct = parseFloat(row.project_implementation);

                    return `
                        <tr>
                            <td class="fw-bold">${row.project}</td>
                            <td class="text-end">${numberFormat(row.expenses)}</td>
                            <td class="text-end">${numberFormat(row.budget)}</td>
                            <td class="text-center">
                                <span class="badge ${pct >= 100 ? 'bg-danger' : (pct >= 75 ? 'bg-warning' : 'bg-success')}">
                                    ${row.budgeted_percentage}
                                </span>
                            </td>
                            <td class="text-end">${numberFormat(row.total_budget)}</td>
                            <td class="text-center">
                                <span class="badge ${implPct >= 100 ? 'bg-danger' : (implPct >= 75 ? 'bg-warning' : 'bg-success')}">
                                    ${row.project_implementation}
                                </span>
                            </td>
                        </tr>
                    `;
                }).join('');
            } else {
                // Show single quarter columns
                thead = `
                    <thead class="table-dark">
                        <tr>
                            <th class="align-middle">Project Name</th>
                            <th class="align-middle text-end">Expenses</th>
                            <th class="align-middle text-end">Budget</th>
                            <th class="align-middle text-center">Budgeted (%)</th>
                            <th class="align-middle text-end">Total Budget (All Projects)</th>
                            <th class="align-middle text-center">Implementation (%)</th>
                        </tr>
                    </thead>
                `;

                tbody = data.report.map(row => {
                    const pct = parseFloat(row.budgeted_percentage);
                    const implPct = parseFloat(row.project_implementation);

                    return `
                        <tr>
                            <td class="fw-bold">${row.project}</td>
                            <td class="text-end">${numberFormat(row.expenses)}</td>
                            <td class="text-end">${numberFormat(row.budget)}</td>
                            <td class="text-center">
                                <span class="badge ${pct >= 100 ? 'bg-danger' : (pct >= 75 ? 'bg-warning' : 'bg-success')}">
                                    ${row.budgeted_percentage}
                                </span>
                            </td>
                            <td class="text-end">${numberFormat(row.total_budget_all || row.total_budget)}</td>
                            <td class="text-center">
                                <span class="badge ${implPct >= 100 ? 'bg-danger' : (implPct >= 75 ? 'bg-warning' : 'bg-success')}">
                                    ${row.project_implementation}
                                </span>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            container.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-primary table-striped mb-0">
                        ${thead}
                        <tbody>${tbody}</tbody>
                    </table>
                </div>
            `;
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
</body>
</html>