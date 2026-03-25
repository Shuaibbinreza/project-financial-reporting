# Financial Reporting System - Database Overview

## Table Definitions and Relationships

This document describes all database tables in the Financial Reporting System and their relationships.

---

## 1. Users Table

**Table Name**: `users`

**Purpose**: Stores system user authentication and profile information.

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique user identifier |
| name | VARCHAR(255) | NOT NULL | User's full name |
| email | VARCHAR(255) | UNIQUE, NOT NULL | User's email address |
| email_verified_at | TIMESTAMP | NULLABLE | Email verification timestamp |
| password | VARCHAR(255) | NOT NULL | Bcrypt hashed password |
| remember_token | VARCHAR(100) | NULLABLE | Token for "remember me" functionality |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships**:
- HasMany `Voucher` (via `created_by` foreign key)

---

## 2. Projects Table

**Table Name**: `projects`

**Purpose**: Stores financial projects being tracked in the system.

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique project identifier |
| name | VARCHAR(255) | NOT NULL | Project name |
| code | VARCHAR(255) | NULLABLE | Project code |
| start_date | DATE | NOT NULL | Project start date |
| end_date | DATE | NOT NULL | Project end date |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships**:
- HasMany `Voucher`
- HasMany `YearlyBudget`

---

## 3. Divisions Table

**Table Name**: `divisions`

**Purpose**: Stores geographic divisions (e.g., Dhaka, Chittagong, Sylhet).

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique division identifier |
| name | VARCHAR(255) | UNIQUE, NOT NULL | Division name |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships**:
- HasMany `District`
- HasMany `Voucher`

---

## 4. Districts Table

**Table Name**: `districts`

**Purpose**: Stores districts within divisions.

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique district identifier |
| division_id | BIGINT | FOREIGN KEY → divisions(id), CASCADE DELETE | Reference to parent division |
| name | VARCHAR(255) | NOT NULL | District name |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Unique Constraint**: `[division_id, name]` - combination must be unique

**Relationships**:
- BelongsTo `Division`
- HasMany `Voucher`

---

## 5. Categories Table

**Table Name**: `categories`

**Purpose**: Stores economic categories for classifying budget codes (e.g., Salaries, Equipment, Travel, Supplies).

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique category identifier |
| name | VARCHAR(255) | UNIQUE, NOT NULL | Category name |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships**:
- HasMany `EconomicCode`
- HasMany `YearlyBudget`
- HasMany `VoucherEntry`

---

## 6. Economic Codes Table

**Table Name**: `economic_codes`

**Purpose**: Stores budget codes belonging to categories. These are the specific line items for tracking expenditures.

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique economic code identifier |
| category_id | BIGINT | FOREIGN KEY → categories(id), CASCADE DELETE | Reference to parent category |
| code | VARCHAR(255) | UNIQUE, NOT NULL | Economic code (e.g., "4501") |
| description | VARCHAR(255) | NULLABLE | Code description |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships**:
- BelongsTo `Category`
- HasMany `YearlyBudget`
- HasMany `VoucherEntry`

---

## 7. Fiscal Years Table

**Table Name**: `fiscal_years`

**Purpose**: Stores fiscal year definitions (July 1 to June 30).

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique fiscal year identifier |
| name | VARCHAR(255) | UNIQUE, NOT NULL | Fiscal year name (e.g., "2025-2026") |
| start_date | DATE | NOT NULL | Fiscal year start date (July 1) |
| end_date | DATE | NOT NULL | Fiscal year end date (June 30) |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships**:
- HasMany `Quarter`
- HasMany `YearlyBudget`

---

## 8. Quarters Table

**Table Name**: `quarters`

**Purpose**: Stores quarterly divisions within fiscal years (Q1, Q2, Q3, Q4).

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique quarter identifier |
| fiscal_year_id | BIGINT | FOREIGN KEY → fiscal_years(id), CASCADE DELETE | Reference to parent fiscal year |
| name | VARCHAR(255) | NOT NULL | Quarter name (e.g., "Jul-Sept 2025") |
| code | VARCHAR(255) | NOT NULL | Quarter code (Q1, Q2, Q3, Q4) |
| quarter_number | INTEGER | NOT NULL | Quarter number (1, 2, 3, 4) |
| start_date | DATE | NOT NULL | Quarter start date |
| end_date | DATE | NOT NULL | Quarter end date |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Unique Constraint**: `[fiscal_year_id, quarter_number]` - each fiscal year has exactly 4 quarters

**Relationships**:
- BelongsTo `FiscalYear`

---

## 9. Yearly Budgets Table

**Table Name**: `yearly_budgets`

**Purpose**: Stores annual budget allocations for each project, category, and economic code combination.

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique yearly budget identifier |
| project_id | BIGINT | FOREIGN KEY → projects(id), CASCADE DELETE | Reference to project |
| fiscal_year_id | BIGINT | FOREIGN KEY → fiscal_years(id), CASCADE DELETE | Reference to fiscal year |
| category_id | BIGINT | FOREIGN KEY → categories(id), CASCADE DELETE | Reference to category |
| economic_code_id | BIGINT | FOREIGN KEY → economic_codes(id), CASCADE DELETE | Reference to economic code |
| total_amount | DECIMAL(15,2) | NOT NULL | Budget amount for the year |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Unique Constraint**: `[project_id, fiscal_year_id, category_id, economic_code_id]` - ensures unique budget per combination

**Relationships**:
- BelongsTo `Project`
- BelongsTo `FiscalYear`
- BelongsTo `Category`
- BelongsTo `EconomicCode`

---

## 10. Vouchers Table

**Table Name**: `vouchers`

**Purpose**: Stores financial voucher documents - the main transaction records in the system.

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique voucher identifier |
| project_id | BIGINT | FOREIGN KEY → projects(id), CASCADE DELETE | Reference to project |
| date | DATE | NOT NULL | Voucher date |
| division_id | BIGINT | FOREIGN KEY → divisions(id), CASCADE DELETE | Reference to division |
| district_id | BIGINT | FOREIGN KEY → districts(id), CASCADE DELETE | Reference to district |
| created_by | BIGINT | FOREIGN KEY → users(id), CASCADE DELETE | User who created the voucher |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships**:
- BelongsTo `Project`
- BelongsTo `Division`
- BelongsTo `District`
- BelongsTo `User` (via `created_by`)
- HasMany `VoucherEntry`

---

## 11. Voucher Entries Table

**Table Name**: `voucher_entries`

**Purpose**: Stores individual line items within vouchers. Each entry represents a single expenditure with category and economic code.

**Columns**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique voucher entry identifier |
| voucher_id | BIGINT | FOREIGN KEY → vouchers(id), CASCADE DELETE | Reference to parent voucher |
| category_id | BIGINT | FOREIGN KEY → categories(id), CASCADE DELETE | Reference to category |
| economic_code_id | BIGINT | FOREIGN KEY → economic_codes(id), CASCADE DELETE | Reference to economic code |
| amount | DECIMAL(15,2) | NOT NULL | Expenditure amount |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships**:
- BelongsTo `Voucher`
- BelongsTo `Category`
- BelongsTo `EconomicCode`

---

## Entity Relationship Diagram

```
┌─────────────────┐       ┌─────────────────┐
│     users       │       │    projects     │
├─────────────────┤       ├─────────────────┤
│ id (PK)         │◄──┐   │ id (PK)         │
│ name            │   │   │ name            │
│ email           │   │   │ code            │
│ password        │   │   │ start_date      │
│ remember_token  │   │   │ end_date        │
└─────────────────┘   │   └─────────────────┘
         │            │          │
         │  created_by          │
         │    1:M   └──────────┐ │
         ▼                    ▼ ▼
┌─────────────────┐    ┌─────────────────┐
│    vouchers     │    │ yearly_budgets  │
├─────────────────┤    ├─────────────────┤
│ id (PK)         │    │ id (PK)         │
│ project_id (FK) │    │ project_id (FK) │
│ date            │    │ fiscal_year_id  │
│ division_id (FK)│    │ category_id (FK)│
│ district_id (FK)│    │ economic_code_id│
│ created_by (FK) │    │ total_amount    │
└────────┬────────┘    └─────────────────┘
         │
         │ 1:M (voucher entries)
         ▼
┌─────────────────────┐
│   voucher_entries   │
├─────────────────────┤
│ id (PK)             │
│ voucher_id (FK)     │
│ category_id (FK)    │
│ economic_code_id(FK)│
│ amount              │
└─────────────────────┘

┌─────────────────┐       ┌─────────────────┐
│    divisions    │       │    districts    │
├─────────────────┤       ├─────────────────┤
│ id (PK)         │◄──────│ id (PK)         │
│ name            │  1:M  │ division_id (FK)│
└─────────────────┘       │ name            │
         │                └─────────────────┘
         │ 1:M                     │
         ▼                         │ 1:M
┌─────────────────┐                ▼
│    vouchers     │         ┌─────────────────┐
├─────────────────┤         │    categories   │
│ division_id (FK)│         ├─────────────────┤
│ district_id (FK)│         │ id (PK)         │
└─────────────────┘         │ name            │
                            └────────┬────────┘
                                     │ 1:M
                                     ▼
                            ┌─────────────────┐
                            │ economic_codes  │
                            ├─────────────────┤
                            │ id (PK)         │
                            │ category_id (FK)│
                            │ code            │
                            │ description     │
                            └─────────────────┘

┌─────────────────┐       ┌─────────────────┐
│  fiscal_years  │       │    quarters     │
├─────────────────┤       ├─────────────────┤
│ id (PK)         │◄──────│ id (PK)         │
│ name            │  1:M  │ fiscal_year_id  │
│ start_date      │       │ name            │
│ end_date        │       │ code            │
└─────────────────┘       │ quarter_number  │
                          │ start_date      │
                          │ end_date        │
                          └─────────────────┘
```

---

## Summary of Relationships

| Parent Table | Child Table | Relationship Type | Description |
|--------------|-------------|-------------------|-------------|
| users | vouchers | 1:M | Each user can create multiple vouchers |
| projects | vouchers | 1:M | Each project can have multiple vouchers |
| projects | yearly_budgets | 1:M | Each project can have multiple yearly budgets |
| divisions | districts | 1:M | Each division can have multiple districts |
| divisions | vouchers | 1:M | Each division can have multiple vouchers |
| districts | vouchers | 1:M | Each district can have multiple vouchers |
| categories | economic_codes | 1:M | Each category can have multiple economic codes |
| categories | yearly_budgets | 1:M | Each category can have multiple yearly budgets |
| categories | voucher_entries | 1:M | Each category can have multiple voucher entries |
| economic_codes | yearly_budgets | 1:M | Each economic code can have multiple yearly budgets |
| economic_codes | voucher_entries | 1:M | Each economic code can have multiple voucher entries |
| fiscal_years | quarters | 1:M | Each fiscal year has exactly 4 quarters |
| fiscal_years | yearly_budgets | 1:M | Each fiscal year can have multiple yearly budgets |
| vouchers | voucher_entries | 1:M | Each voucher can have multiple entries |

---

## Database Indexes

| Table | Index | Type | Columns |
|-------|-------|------|---------|
| districts | divisions_division_id_foreign | FOREIGN KEY | division_id |
| economic_codes | economic_codes_category_id_foreign | FOREIGN KEY | category_id |
| vouchers | vouchers_project_id_foreign | FOREIGN KEY | project_id |
| vouchers | vouchers_division_id_foreign | FOREIGN KEY | division_id |
| vouchers | vouchers_district_id_foreign | FOREIGN KEY | district_id |
| vouchers | vouchers_created_by_foreign | FOREIGN KEY | created_by |
| voucher_entries | voucher_entries_voucher_id_foreign | FOREIGN KEY | voucher_id |
| voucher_entries | voucher_entries_category_id_foreign | FOREIGN KEY | category_id |
| voucher_entries | voucher_entries_economic_code_id_foreign | FOREIGN KEY | economic_code_id |
| yearly_budgets | yearly_budget_unique | UNIQUE | project_id, fiscal_year_id, category_id, economic_code_id |
| quarters | quarters_fiscal_year_id_quarter_number_unique | UNIQUE | fiscal_year_id, quarter_number |
