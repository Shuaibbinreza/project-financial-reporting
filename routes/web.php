<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialReportController;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/reports/financial', [FinancialReportController::class, 'index'])->name('reports.financial');
Route::get('/reports/cutoff', [FinancialReportController::class, 'cutoffReport'])->name('reports.cutoff');
Route::get('/reports/category-summary', [FinancialReportController::class, 'categorySummary'])->name('reports.categorySummary');
