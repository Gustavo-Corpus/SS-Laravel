<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Usuario extends Model
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuarios';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'edad',
        'sexo',
        'estatus',
        'correo',
        'ocupacion',
        'avatar',
        'id_departamento',
        'id_estado',
        'rol',
        'calificacion_promedio',
        'tickets_resueltos'
    ];

    // Relaciones existentes
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'id_usuario', 'id_usuarios');
    }

    // Nuevas relaciones para tickets
    public function ticketsAsignados()
    {
        return $this->hasMany(Ticket::class, 'id_asignado', 'id_usuarios');
    }

    public function ticketsCreados()
    {
        return $this->hasMany(Ticket::class, 'id_cliente', 'id_usuarios');
    }

    public function calificacionesRecibidas()
    {
        return $this->hasMany(CalificacionTicket::class, 'id_empleado', 'id_usuarios');
    }

    // Helpers
    public function esAdmin()
    {
        return $this->rol === 'admin';
    }

    public function esEmpleado()
    {
        return $this->rol === 'empleado';
    }

    public function esCliente()
    {
        return $this->rol === 'cliente';
    }

    public function routeNotificationForMail()
    {
        return $this->correo;
    }
}
