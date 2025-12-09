<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'user_id',
        'estrellas',
        'comentario',
        'fecha'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
