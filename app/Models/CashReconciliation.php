<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'petty',
        'bill_20',
        'bill_10',
        'bill_5',
        'bill_1',
        'coin_10',
        'coin_5',
        'coin_1',
        'coin_25',
        'total_counted',
        'total_sales_cash',
        'difference',
    ];
}
