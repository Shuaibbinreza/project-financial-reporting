<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialReportController;

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
Route::get('/', [FinancialReportController::class, 'dashboard'])->name('home');

// Reports Routes - Grouped
Route::prefix('reports')->name('reports.')->group(function () {
    // Main report pages
    Route::get('/financial', [FinancialReportController::class, 'index'])->name('financial');
    Route::get('/cutoff', [FinancialReportController::class, 'cutoffReport'])->name('cutoff');
    Route::get('/category-summary', [FinancialReportController::class, 'categorySummary'])->name('category-summary');
    Route::get('/project-summary', [FinancialReportController::class, 'projectSummary'])->name('project-summary');

    // AJAX endpoints
    Route::get('/category-summary/ajax', [FinancialReportController::class, 'categorySummaryAjax'])->name('category-summary-ajax');
    Route::get('/project-summary/ajax', [FinancialReportController::class, 'projectSummaryAjax'])->name('project-summary-ajax');
    Route::get('/get-districts', [FinancialReportController::class, 'getDistricts'])->name('get-districts');
});
