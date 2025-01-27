<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'username',
        'nombre',
        'apellido',
        'name',
        'email',
        'password',
        'rol',
        'department_id',
        'estado_id',
        'average_rating',
        'tickets_resolved'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'average_rating' => 'decimal:2',
    ];

    // Relaciones
    public function department()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function ticketsAsignados()
    {
        return $this->hasMany(Ticket::class, 'id_asignado', 'id');
    }

    public function calificacionesRecibidas()
    {
        return $this->hasMany(CalificacionTicket::class, 'id_empleado', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_usuario', 'id');
    }

    // Helpers
    public function isAdmin()
    {
        return $this->rol === 'admin';
    }

    public function isEmployee()
    {
        return $this->rol === 'employee';
    }

    public function isClient()
    {
        return $this->rol === 'client';
    }
}
