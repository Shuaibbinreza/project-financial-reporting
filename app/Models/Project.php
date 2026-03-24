<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'start_date', 'end_date'];

    // Project has many yearly budgets
    public function yearlyBudgets()
    {
        return $this->hasMany(YearlyBudget::class);
    }

    // Project has many vouchers
    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
