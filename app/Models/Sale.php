<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $connection = 'tenant';

    protected $fillable = ['cliente_id', 'cajero_id', 'total', 'metodo_pago'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function cajero()
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }
}
