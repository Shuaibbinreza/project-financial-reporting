<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category-wise Financial Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-1">
                    <i class="bi bi-pie-chart me-2"></i>Category-wise Financial Summary
                </h1>
                <p class="text-muted mb-0">
                    @switch($selectedQuarter)
                        @case('Q1')
                            Showing data from July to September (Q1 - Cumulative)
                            @break
                        @case('Q2')
                            Showing data from July to December (Q2 - Cumulative)
                            @break
                        @case('Q3')
                            Showing data from July to March (Q3 - Cumulative)
                            @break
                        @case('Q4')
                            Showing data from July to June (Q4 - Full Year Cumulative)
                            @break
                        @default
                            Showing data from July to June (Q4 - Default)
                    @endswitch
                </p>
            </div>
            <div class="col-auto">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-house me-1"></i>Home
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
                <form method="GET" action="{{ route('reports.categorySummary') }}" class="row g-3">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label for="quarter" class="form-label">Quarter</label>
                        <select name="quarter" id="quarter" class="form-select">
                            <option value="all" {{ request('quarter') == 'all' || !request('quarter') ? 'selected' : '' }}>All (Default to Q4)</option>
                            <option value="Q4" {{ request('quarter') == 'Q4' ? 'selected' : '' }}>Q4 (Till June)</option>
                            <option value="Q3" {{ request('quarter') == 'Q3' ? 'selected' : '' }}>Q3 (Till March)</option>
                            <option value="Q2" {{ request('quarter') == 'Q2' ? 'selected' : '' }}>Q2 (Till December)</option>
                            <option value="Q1" {{ request('quarter') == 'Q1' ? 'selected' : '' }}>Q1 (Till September)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('reports.categorySummary') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Table Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-table me-2"></i>Category Summary - {{ $selectedQuarter }}
                </h5>
                <span class="badge bg-secondary">{{ count($report) }} Categories</span>
            </div>
            <div class="card-body p-0">
                @if(count($report) > 0)
                    <div class="table-responsive">
                        <table class="table table-primary table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="align-middle">Cost Category</th>
                                    <th class="align-middle text-end">Expenses</th>
                                    <th class="align-middle text-end">Budget</th>
                                    <th class="align-middle text-center">Budgeted (%)</th>
                                    <th class="align-middle text-end">Total Project Budget</th>
                                    <th class="align-middle text-center">Implementation (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report as $row)
                                <tr>
                                    <td class="fw-bold">{{ $row['category'] }}</td>
                                    <td class="text-end">{{ number_format($row['expenses']) }}</td>
                                    <td class="text-end">{{ number_format($row['budget']) }}</td>
                                    <td class="text-center">
                                        @php $pct = floatval($row['budgeted_percentage']); @endphp
                                        <span class="badge {{ $pct >= 100 ? 'bg-danger' : ($pct >= 75 ? 'bg-warning' : 'bg-success') }}">
                                            {{ $row['budgeted_percentage'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ number_format($row['total_project_budget']) }}</td>
                                    <td class="text-center">
                                        @php $implPct = floatval($row['project_implementation']); @endphp
                                        <span class="badge {{ $implPct >= 100 ? 'bg-danger' : ($implPct >= 75 ? 'bg-warning' : 'bg-success') }}">
                                            {{ $row['project_implementation'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <p class="text-muted mt-3">No data available for the selected filters</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>