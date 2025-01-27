<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'numero_ticket',
        'id_usuario',
        'id_asignado',
        'titulo',
        'descripcion',
        'prioridad',
        'estado'
    ];

    // Relación con el usuario que creó el ticket
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    // Relación con el empleado asignado
    public function empleadoAsignado()
    {
        return $this->belongsTo(User::class, 'id_asignado', 'id');
    }

    public function calificaciones()
    {
        return $this->hasMany(CalificacionTicket::class, 'id_ticket');
    }
}
