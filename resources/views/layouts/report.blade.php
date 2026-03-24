@extends('layouts.main')

@section('title', $title ?? 'Report')

@section('styles')
<style>
    .filter-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 0.5rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .filter-label {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .btn-group-filter .btn {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    .btn-group-filter .btn.active {
        font-weight: 600;
    }

    .report-table {
        font-size: 0.875rem;
    }

    .report-table th {
        background-color: #1a1a2e !important;
        color: #fff !important;
        white-space: nowrap;
    }

    .report-table td {
        vertical-align: middle;
    }

    .table-quarter {
        background-color: #f8f9fa;
    }

    .table-quarter th {
        background-color: #e9ecef !important;
        color: #333 !important;
    }

    .amount-positive {
        color: #198754;
        font-weight: 500;
    }

    .amount-negative {
        color: #dc3545;
        font-weight: 500;
    }

    .badge-quarter {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }

    .quick-link-card {
        transition: all 0.2s;
        cursor: pointer;
    }

    .quick-link-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .page-breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 0;
    }

    .page-breadcrumb .breadcrumb {
        margin: 0;
        padding: 0;
        background: transparent;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse show">
            <div class="position-sticky pt-3">
                <div class="sidebar-brand text-white mb-4">
                    <i class="bi bi-building me-2"></i>{{ config('app.name', 'Financial Reporting') }}
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="sidebar-link text-white-50 small text-uppercase mt-3 mb-2">Reports</span>
                    </li>
                    <li class="nav-item">
                        <a class="sidebar-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}" href="{{ route('reports.financial') }}">
                            <i class="bi bi-graph-up-arrow"></i>Financial Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="sidebar-link {{ request()->routeIs('reports.project-summary') ? 'active' : '' }}" href="{{ route('reports.project-summary') }}">
                            <i class="bi bi-briefcase"></i>Project Summary
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="sidebar-link {{ request()->routeIs('reports.category-summary') ? 'active' : '' }}" href="{{ route('reports.category-summary') }}">
                            <i class="bi bi-pie-chart"></i>Category Summary
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="sidebar-link {{ request()->routeIs('reports.cutoff') ? 'active' : '' }}" href="{{ route('reports.cutoff') }}">
                            <i class="bi bi-file-earmark-ruled"></i>Cutoff Report
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="page-breadcrumb mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb ?? 'Report' }}</li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="page-header mt-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="h3 mb-1">
                            <i class="{{ $icon ?? 'bi bi-file-earmark-text' }} me-2"></i>{{ $title ?? 'Report' }}
                        </h1>
                        <p class="mb-0 opacity-75">{{ $description ?? 'View and filter financial data' }}</p>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button class="btn btn-outline-light" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <button class="btn btn-outline-light" id="exportBtn">
                                <i class="bi bi-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            @yield('quick-links')

            <!-- Filters -->
            @yield('filters')

            <!-- Report Content -->
            @yield('report-content')

            <!-- Footer -->
            <footer class="mt-4 py-3 border-top">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Financial Reporting') }}. All rights reserved.</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <a href="#" class="me-3">
                                <i class="bi bi-book me-1"></i>Documentation
                            </a>
                            <a href="#">
                                <i class="bi bi-question-circle me-1"></i>Support
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </main>
    </div>
</div>
@endsection
