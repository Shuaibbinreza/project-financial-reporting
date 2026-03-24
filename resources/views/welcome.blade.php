@extends('layouts.main')

@section('title', 'Dashboard')

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
            <!-- Top Navigation -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card stat-card primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase mb-1">Total Projects</h6>
                                    <h2 class="mb-0">{{ $stats['total_projects'] ?? 0 }}</h2>
                                </div>
                                <div class="feature-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-briefcase"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase mb-1">Total Budget</h6>
                                    <h2 class="mb-0">{{ number_format($stats['total_budget'] ?? 0) }}</h2>
                                </div>
                                <div class="feature-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase mb-1">Total Expenses</h6>
                                    <h2 class="mb-0">{{ number_format($stats['total_expenses'] ?? 0) }}</h2>
                                </div>
                                <div class="feature-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-cart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted text-uppercase mb-1">Districts</h6>
                                    <h2 class="mb-0">{{ $stats['total_districts'] ?? 0 }}</h2>
                                </div>
                                <div class="feature-icon bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-lightning me-2"></i>Quick Access
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <a href="{{ route('reports.financial') }}" class="text-decoration-none">
                                        <div class="card h-100 border-primary">
                                            <div class="card-body text-center">
                                                <i class="bi bi-graph-up-arrow text-primary fs-2"></i>
                                                <h5 class="mt-2 text-dark">Financial Reports</h5>
                                                <p class="text-muted small mb-0">Quarterly financial data</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('reports.project-summary') }}" class="text-decoration-none">
                                        <div class="card h-100 border-info">
                                            <div class="card-body text-center">
                                                <i class="bi bi-briefcase text-info fs-2"></i>
                                                <h5 class="mt-2 text-dark">Project Summary</h5>
                                                <p class="text-muted small mb-0">Budget vs Expenses</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('reports.category-summary') }}" class="text-decoration-none">
                                        <div class="card h-100 border-success">
                                            <div class="card-body text-center">
                                                <i class="bi bi-pie-chart text-success fs-2"></i>
                                                <h5 class="mt-2 text-dark">Category Summary</h5>
                                                <p class="text-muted small mb-0">Category breakdown</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('reports.cutoff') }}" class="text-decoration-none">
                                        <div class="card h-100 border-warning">
                                            <div class="card-body text-center">
                                                <i class="bi bi-file-earmark-ruled text-warning fs-2"></i>
                                                <h5 class="mt-2 text-dark">Cutoff Report</h5>
                                                <p class="text-muted small mb-0">Project status as of date</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-info-circle me-2"></i>Key Features
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="feature-icon bg-primary bg-opacity-10 text-primary me-3 flex-shrink-0">
                                            <i class="bi bi-folder2-open"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Project Management</h5>
                                            <p class="text-muted mb-0 small">Organize and track multiple projects with detailed budget allocations.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="feature-icon bg-success bg-opacity-10 text-success me-3 flex-shrink-0">
                                            <i class="bi bi-pie-chart"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Financial Reports</h5>
                                            <p class="text-muted mb-0 small">Generate quarterly financial reports with budget vs. expense analysis.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="feature-icon bg-info bg-opacity-10 text-info me-3 flex-shrink-0">
                                            <i class="bi bi-diagram-3"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Division Tracking</h5>
                                            <p class="text-muted mb-0 small">Monitor financial data across multiple divisions and districts.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
