<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherEntry extends Model
{
    use HasFactory;

    protected $fillable = ['voucher_id', 'category_id', 'economic_code_id', 'amount'];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
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