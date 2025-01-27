<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalificacionTicket extends Model
{
    protected $table = 'calificaciones_tickets';

    protected $fillable = [
        'id_ticket',
        'id_usuario',
        'id_empleado',
        'calificacion',
        'comentario'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id_ticket');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function empleado()
    {
        return $this->belongsTo(User::class, 'id_empleado');
    }
}
