<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'event',
        'ip_address',
        'user_agent'
    ];

    // 👇 Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
