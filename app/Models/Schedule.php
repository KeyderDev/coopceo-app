<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = ['user_id', 'day', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
