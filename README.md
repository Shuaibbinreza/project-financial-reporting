# Financial Reporting System

A Laravel-based financial reporting system for managing vouchers, tracking project spendings, and generating various financial reports.

## Features

- **Voucher Management**: Create, edit, and manage financial vouchers with multiple entries
- **Project Tracking**: Track spending by project with budget monitoring
- **Category Summaries**: Financial summaries by economic categories
- **Fiscal Year Support**: Multi-year fiscal data management with quarterly breakdowns
- **Cutoff Reports**: Generate cutoff reports for financial periods
- **Data Export**: Export reports to PDF and Excel formats

## Tech Stack

- **Framework**: Laravel 13.x
- **PHP**: 8.3+
- **Database**: MySQL/SQLite
- **PDF Generation**: barryvdh/laravel-dompdf
- **Excel Export**: maatwebsite/excel
- **Frontend**: Bootstrap 5, DataTables

## Installation

### Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js & NPM
- MySQL or SQLite database

### Setup

1. Clone the repository and install dependencies:
```bash
composer install
```

2. Copy the environment file:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Configure your database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=financial_reporting
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seeders:
```bash
php artisan migrate --force
php artisan db:seed
```

6. Build frontend assets:
```bash
npm install
npm run build
```

### Quick Setup (Alternative)

Run the setup script:
```bash
composer run setup
```

## Running the Application

Start the development server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Project Structure

```
project-financial-reporting/
├── app/
│   ├── Http/Controllers/
│   │   ├── FinancialReportController.php  # Report generation logic
│   │   └── VoucherController.php         # Voucher management
│   ├── Models/
│   │   ├── Category.php                  # Economic categories
│   │   ├── District.php                   # Geographic districts
│   │   ├── Division.php                   # Geographic divisions
│   │   ├── EconomicCode.php               # Budget codes
│   │   ├── FiscalYear.php                 # Fiscal year data
│   │   ├── Project.php                    # Project information
│   │   ├── Quarter.php                    # Quarterly data
│   │   ├── Voucher.php                    # Voucher headers
│   │   ├── VoucherEntry.php              # Voucher line items
│   │   └── YearlyBudget.php              # Annual budgets
│   └── Services/
│       └── FinancialReportService.php    # Report calculations
├── database/
│   ├── migrations/                        # Database schema
│   └── seeders/                           # Sample data
├── resources/views/
│   ├── layouts/                           # Master templates
│   ├── reports/                           # Report views
│   └── vouchers/                         # Voucher views
└── routes/
    └── web.php                            # Web routes
```

## Available Routes

| Route | Description |
|-------|-------------|
| `/` | Dashboard/Welcome page |
| `/vouchers` | Voucher list |
| `/vouchers/entries` | Voucher entry management |
| `/reports/financial` | Financial reports |
| `/reports/project-summary` | Project summary reports |
| `/reports/category-summary` | Category summary reports |
| `/reports/project-spendings` | Project spending reports |
| `/reports/cutoff` | Cutoff reports |

## Database Models

### Core Entities

- **Projects**: Financial projects being tracked
- **Divisions**: Geographic divisions (e.g., Dhaka, Chittagong)
- **Districts**: Districts within divisions
- **Categories**: Economic categories for classification
- **Economic Codes**: Budget codes for accounting
- **Fiscal Years**: Financial years with start/end dates
- **Quarters**: Q1, Q2, Q3, Q4 within fiscal years
- **Yearly Budgets**: Annual budget allocations
- **Vouchers**: Financial voucher documents
- **Voucher Entries**: Individual line items in vouchers

## Commands

### Clear Application Cache
```bash
php artisan cache:clear
```

### View Routes
```bash
php artisan route:list
```

### Run Tests
```bash
composer run test
```

## Development

### Running in Development Mode
```bash
composer run dev
```

This starts:
- Laravel development server
- Queue listener
- Log watcher
- Vite dev server

## License

This project is licensed under the MIT License.
