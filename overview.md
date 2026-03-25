# Financial Reporting System - Project Overview

## Introduction

This is a Laravel-based Financial Reporting System designed to manage financial vouchers, track project spending, and generate various financial reports. The system provides comprehensive tools for budget monitoring, fiscal year management, and data export capabilities.

## Technology Stack

| Component | Technology |
|-----------|-------------|
| Framework | Laravel 13.x |
| PHP | 8.3+ |
| Database | MySQL/SQLite |
| PDF Generation | barryvdh/laravel-dompdf |
| Excel Export | maatwebsite/excel |
| Frontend | Bootstrap 5, DataTables |
| JavaScript | Vanilla JS |

## Key Features

### 1. Voucher Management
- Create, edit, and delete financial vouchers
- Multiple entries per voucher with different categories and economic codes
- Link vouchers to projects, divisions, and districts
- Track voucher dates and creators

### 2. Project Tracking
- Manage multiple projects
- Track spending per project
- Budget vs expense analysis
- Quarterly spending breakdowns

### 3. Financial Reports
- **Financial Report**: Filterable by project, division, district, category, economic code, date range, and quarter
- **Project Summary**: Budget vs expense comparison per project
- **Category Summary**: Spending by economic categories
- **Project Spendings**: Detailed spending records under each project
- **Cutoff Report**: Financial data cutoff for specific periods

### 4. Geographic Management
- Division management (e.g., Dhaka, Chittagong)
- District management within divisions
- Geographic filtering in reports

### 5. Fiscal Year Support
- Multi-year fiscal data management
- Automatic quarterly breakdowns (Q1, Q2, Q3, Q4)
- July-June fiscal year format

### 6. Data Export
- PDF export for all reports
- Excel export functionality
- CSV export
- Copy to clipboard
- Print support

## Database Schema

### Core Models

```
Users
├── id
├── name
├── email
└── password

Projects
├── id
├── name
├── code
├── start_date
└── end_date

Divisions
├── id
└── name

Districts
├── id
├── name
└── division_id

Categories
├── id
├── name
└── code

EconomicCodes
├── id
├── code
├── name
└── category_id

FiscalYears
├── id
├── name
├── start_date
└── end_date

Quarters
├── id
├── fiscal_year_id
├── name
├── code
├── quarter_number
├── start_date
└── end_date

YearlyBudgets
├── id
├── project_id
├── fiscal_year_id
├── category_id
├── economic_code_id
└── total_amount

Vouchers
├── id
├── project_id
├── date
├── division_id
├── district_id
├── created_by
└── created_at

VoucherEntries
├── id
├── voucher_id
├── category_id
├── economic_code_id
└── amount
```

## Project Structure

```
project-financial-reporting/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── FinancialReportController.php
│   │       └── VoucherController.php
│   ├── Models/
│   │   ├── Category.php
│   │   ├── District.php
│   │   ├── Division.php
│   │   ├── EconomicCode.php
│   │   ├── FiscalYear.php
│   │   ├── Project.php
│   │   ├── Quarter.php
│   │   ├── User.php
│   │   ├── Voucher.php
│   │   ├── VoucherEntry.php
│   │   └── YearlyBudget.php
│   └── Services/
│       └── FinancialReportService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── reports/
│       └── vouchers/
└── routes/
    └── web.php
```

## Available Routes

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/` | Controller | Dashboard/Welcome |
| GET | `/vouchers` | VoucherController | Voucher list |
| POST | `/vouchers` | VoucherController | Create voucher |
| GET | `/vouchers/{id}` | VoucherController | View voucher |
| PUT | `/vouchers/{id}` | VoucherController | Update voucher |
| DELETE | `/vouchers/{id}` | VoucherController | Delete voucher |
| GET | `/vouchers/entries` | VoucherController | Voucher entry management |
| POST | `/vouchers/entries` | VoucherController | Create entry |
| GET | `/reports/financial` | FinancialReportController | Financial reports |
| GET | `/reports/project-summary` | FinancialReportController | Project summary |
| GET | `/reports/category-summary` | FinancialReportController | Category summary |
| GET | `/reports/project-spendings` | FinancialReportController | Project spendings |
| GET | `/reports/cutoff` | FinancialReportController | Cutoff reports |

## Installation

### Prerequisites
- PHP 8.3+
- Composer
- Node.js & NPM
- MySQL or SQLite

### Setup Steps

1. Install dependencies:
```bash
composer install
```

2. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Run migrations:
```bash
php artisan migrate --force
php artisan db:seed
```

4. Build assets:
```bash
npm install
npm run build
```

5. Run server:
```bash
php artisan serve
```

## Usage

### Creating a Voucher
1. Navigate to `/vouchers`
2. Click "Create New Voucher"
3. Select project, division, district
4. Add entries with category, economic code, and amount
5. Save voucher

### Generating Reports
1. Navigate to desired report (e.g., `/reports/financial`)
2. Apply filters (project, division, date range, quarter)
3. Click "Filter" to refresh data
4. Use export buttons to download PDF/Excel/CSV

## Development

### Running Tests
```bash
composer run test
```

### Development Mode
```bash
composer run dev
```

## License

MIT License
