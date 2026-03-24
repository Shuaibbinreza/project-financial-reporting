@extends('layouts.report')

@section('title', 'Cutoff Report')
@section('breadcrumb', 'Cutoff Report')
@section('icon', 'bi bi-file-earmark-ruled')
@section('description', 'Project financial status as of specific date')

@section('quick-links')
<div class="mb-3">
    <div class="btn-group" role="group">
        <a href="{{ route('reports.financial') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-bar-chart me-1"></i>Financial Report
        </a>
        <a href="{{ route('reports.project-summary') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-briefcase me-1"></i>Project Summary
        </a>
        <a href="{{ route('reports.category-summary') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pie-chart me-1"></i>Category Summary
        </a>
    </div>
</div>
@endsection

@section('report-content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-table me-2"></i>Project Financial Status as of {{ $cutoffDate->format('d M, Y') }}
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
                            <th>Cost Category</th>
                            <th class="text-end">Expense</th>
                            <th class="text-end">Budget</th>
                            <th class="text-center">Budgeted (%)</th>
                            <th class="text-end">Total Project Budget</th>
                            <th class="text-center">Implementation (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report as $row)
                        <tr>
                            <td class="fw-bold">{{ $row['project'] }}</td>
                            <td>{{ $row['category'] }}</td>
                            <td class="text-end">{{ number_format($row['expenses']) }}</td>
                            <td class="text-end">{{ number_format($row['budget']) }}</td>
                            <td class="text-center">
                                <span class="badge {{ floatval($row['budgeted_percentage']) >= 100 ? 'bg-danger' : (floatval($row['budgeted_percentage']) >= 75 ? 'bg-warning' : 'bg-success') }}">
                                    {{ $row['budgeted_percentage'] }}
                                </span>
                            </td>
                            <td class="text-end">{{ number_format($row['total_project_budget']) }}</td>
                            <td class="text-center">
                                <span class="badge {{ floatval($row['project_implementation']) >= 100 ? 'bg-danger' : (floatval($row['project_implementation']) >= 75 ? 'bg-warning' : 'bg-success') }}">
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
                <p class="text-muted mt-3">No data available</p>
            </div>
        @endif
    </div>
</div>
@endsection
