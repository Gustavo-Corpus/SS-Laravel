<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    public $timestamps = false; // Si tu tabla no tiene timestamps

    protected $fillable = [
        'username',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    // Agregar si tu tabla tiene una columna remember_token
    protected $casts = [
        'password' => 'string',
    ];

    // Si quieres usar el username en lugar del email para autenticación
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}
