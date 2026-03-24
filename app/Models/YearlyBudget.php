<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyBudget extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'fiscal_year_id', 'category_id', 'economic_code_id', 'total_amount'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function economicCode()
    {
        return $this->belongsTo(EconomicCode::class);
    }
}