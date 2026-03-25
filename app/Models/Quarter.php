<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quarter extends Model
{
    use HasFactory;

    protected $fillable = ['fiscal_year_id', 'name', 'code', 'quarter_number', 'start_date', 'end_date'];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function yearlyBudgets()
    {
        return $this->hasMany(YearlyBudget::class);
    }
}