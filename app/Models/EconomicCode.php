<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EconomicCode extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'code', 'description'];

    public function category()
    {
        return $this->belongsTo(Category::class);
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