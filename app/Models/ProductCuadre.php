<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCuadre extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = ['product_id', 'stock', 'contado'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
