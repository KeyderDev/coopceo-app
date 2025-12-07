<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'tenant';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'numero_socio',
        'dividendos',
        'admin',
        'password',
        'api_token',
    ];

    protected $hidden = [
        'password',
        'api_token',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'cliente_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
}
