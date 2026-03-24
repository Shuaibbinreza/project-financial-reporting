<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialReportController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/reports/financial', [FinancialReportController::class, 'index'])->name('reports.financial');
