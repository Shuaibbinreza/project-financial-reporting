<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Financial Reporting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            min-height: 60vh;
        }
        .feature-card {
            transition: transform 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-building me-2"></i>{{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Log in
                                </a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <i class="bi bi-person-plus me-1"></i>Register
                                    </a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Project Financial Reporting</h1>
                    <p class="lead mb-4">A comprehensive solution for tracking and managing project budgets, expenses, and financial reports across multiple divisions.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('reports.financial') }}" class="btn btn-light btn-lg">
                            <i class="bi bi-graph-up me-2"></i>View Reports
                        </a>
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Get Started
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6 text-center d-none d-lg-block">
                    <i class="bi bi-pie-chart" style="font-size: 10rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Key Features</h2>
                <p class="text-muted">Everything you need to manage project finances effectively</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm feature-card">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-folder2-open text-primary fs-4"></i>
                            </div>
                            <h5 class="card-title">Project Management</h5>
                            <p class="card-text text-muted">Organize and track multiple projects with detailed budget allocations.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm feature-card">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-pie-chart text-primary fs-4"></i>
                            </div>
                            <h5 class="card-title">Financial Reports</h5>
                            <p class="card-text text-muted">Generate quarterly financial reports with budget vs. expense analysis.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm feature-card">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-diagram-3 text-primary fs-4"></i>
                            </div>
                            <h5 class="card-title">Division Tracking</h5>
                            <p class="card-text text-muted">Monitor financial data across multiple divisions and districts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                <i class="bi bi-file-earmark-text me-2"></i>Financial Reports
                            </h5>
                            <p class="card-text">Access quarterly financial reports with filtering options by project, division, and time period.</p>
                            <a href="{{ route('reports.financial') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-right me-1"></i>View Reports
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body">
                            <h5 class="card-title text-info">
                                <i class="bi bi-briefcase me-2"></i>Project Summary
                            </h5>
                            <p class="card-text">View project-wise financial summary with budget vs. expense analysis across all projects.</p>
                            <a href="{{ route('reports.projectSummary') }}" class="btn btn-info">
                                <i class="bi bi-arrow-right me-1"></i>View Projects
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-secondary">
                        <div class="card-body">
                            <h5 class="card-title text-secondary">
                                <i class="bi bi-question-circle me-2"></i>Need Help?
                            </h5>
                            <p class="card-text">Learn more about how to use the financial reporting system and generate detailed reports.</p>
                            <a href="https://laravel.com/docs" target="_blank" class="btn btn-outline-secondary">
                                <i class="bi bi-book me-1"></i>Documentation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="https://laravel.com/docs" class="text-white text-decoration-none me-3" target="_blank">
                        <i class="bi bi-book me-1"></i>Documentation
                    </a>
                    <a href="https://laracasts.com" class="text-white text-decoration-none" target="_blank">
                        <i class="bi bi-play-circle me-1"></i>Tutorials
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
