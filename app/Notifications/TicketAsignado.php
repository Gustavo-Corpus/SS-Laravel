<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketAsignado extends Notification
{
    use Queueable;

    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'titulo' => $this->ticket->titulo,
            'mensaje' => "Nuevo ticket asignado: {$this->ticket->titulo}",
            'numero_ticket' => $this->ticket->numero_ticket,
            'prioridad' => $this->ticket->prioridad
        ];
    }
}
