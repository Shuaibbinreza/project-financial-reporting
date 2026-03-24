<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function economicCodes()
    {
        return $this->hasMany(EconomicCode::class);
    }

    public function voucherEntries()
    {
        return $this->hasMany(VoucherEntry::class);
    }

    public function yearlyBudgets()
    {
        return $this->hasMany(YearlyBudget::class);
    }
}
