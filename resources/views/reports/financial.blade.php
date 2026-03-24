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
            <div class="col-md-3">
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
            <div class="col-md-3">
                <label for="division_ids" class="form-label">Division(s)</label>
                <select name="division_ids[]" id="division_ids" class="form-select" multiple>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ in_array($division->id, request('division_ids', [])) ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
            </div>
            <div class="col-md-3">
                <label for="quarter" class="form-label">Quarter</label>
                <select name="quarter" id="quarter" class="form-select">
                    <option value="">All Quarters</option>
                    <option value="Q1" {{ request('quarter') == 'Q1' ? 'selected' : '' }}>Q1</option>
                    <option value="Q2" {{ request('quarter') == 'Q2' ? 'selected' : '' }}>Q2</option>
                    <option value="Q3" {{ request('quarter') == 'Q3' ? 'selected' : '' }}>Q3</option>
                    <option value="Q4" {{ request('quarter') == 'Q4' ? 'selected' : '' }}>Q4</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="btn-group w-100" role="group">
                    <button type="submit" class="btn btn-primary">
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
            <i class="bi bi-table me-2"></i>Report Data
        </h5>
        <span class="badge bg-secondary">{{ count($report) }} Projects</span>
    </div>
    <div class="card-body p-0">
        @if(count($report) > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 report-table">
                    <thead class="table-dark">
                        <tr>
                            <th>Project</th>
                            <th>Category</th>
                            <th>Economic Code</th>
                            <th>Quarter</th>
                            <th class="text-end">Budget</th>
                            <th class="text-end">Expenses</th>
                            <th class="text-center">% Spent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report as $row)
                            @foreach($row['quarters'] as $quarter => $data)
                            <tr>
                                <td>{{ $row['project'] }}</td>
                                <td>{{ $row['category'] }}</td>
                                <td>{{ $row['economic_code'] }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $quarter }}</span>
                                </td>
                                <td class="text-end">{{ number_format($data['budget']) }}</td>
                                <td class="text-end">{{ number_format($data['expenses']) }}</td>
                                <td class="text-center">
                                    @php
                                        $percent = floatval($data['percent_spent']);
                                        $badgeClass = $percent >= 100 ? 'bg-danger' : ($percent >= 75 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $data['percent_spent'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                            <!-- Yearly Total Row -->
                            <tr class="table-warning fw-bold">
                                <td colspan="4">
                                    <i class="bi bi-calculator me-1"></i>Yearly Total
                                </td>
                                <td class="text-end">{{ number_format($row['yearly_total']['budget']) }}</td>
                                <td class="text-end">{{ number_format($row['yearly_total']['expenses']) }}</td>
                                <td class="text-center">
                                    @php
                                        $yearlyPercent = floatval($row['yearly_total']['percent_spent']);
                                        $yearlyBadgeClass = $yearlyPercent >= 100 ? 'bg-danger' : ($yearlyPercent >= 75 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <span class="badge {{ $yearlyBadgeClass }}">{{ $row['yearly_total']['percent_spent'] }}</span>
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
@endsection
