<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\VoucherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Home / Dashboard
Route::get('/', [ReportController::class, 'index'])->name('home');
Route::get('/home', [ReportController::class, 'index'])->name('home');
Route::get('/dashboard', [ReportController::class, 'index'])->name('dashboard');

// Reports Routes - Grouped
Route::prefix('reports')->name('reports.')->group(function () {
    // Main report pages
    Route::get('/financial', [FinancialReportController::class, 'index'])->name('financial');
    Route::get('/cutoff', [FinancialReportController::class, 'cutoffReport'])->name('cutoff');
    Route::get('/category-summary', [FinancialReportController::class, 'categorySummary'])->name('category-summary');
    Route::get('/project-summary', [FinancialReportController::class, 'projectSummary'])->name('project-summary');
    Route::get('/voucher-summary', [ReportController::class, 'voucherSummary'])->name('voucher-summary');
    Route::get('/voucher-entries', [ReportController::class, 'voucherEntries'])->name('voucher-entries');

    // AJAX endpoints
    Route::get('/category-summary/ajax', [FinancialReportController::class, 'categorySummaryAjax'])->name('category-summary-ajax');
    Route::get('/project-summary/ajax', [FinancialReportController::class, 'projectSummaryAjax'])->name('project-summary-ajax');
    Route::get('/get-districts', [ReportController::class, 'getDistricts'])->name('get-districts');
    Route::get('/voucher-summary/data', [ReportController::class, 'voucherSummaryData'])->name('voucher-summary-data');
});

// Voucher Routes
Route::prefix('vouchers')->name('vouchers.')->group(function () {
    Route::get('/', [VoucherController::class, 'index'])->name('index');
    Route::get('/data', [VoucherController::class, 'data'])->name('data');
    Route::get('/entries-data', [VoucherController::class, 'entriesData'])->name('entries-data');
});
