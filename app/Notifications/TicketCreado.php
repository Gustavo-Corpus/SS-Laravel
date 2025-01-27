<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketCreado extends Notification
{
    use Queueable;

    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nuevo Ticket de Soporte: ' . $this->ticket->numero_ticket)
            ->line('Se ha creado un nuevo ticket de soporte.')
            ->line('Título: ' . $this->ticket->titulo)
            ->line('Prioridad: ' . ucfirst($this->ticket->prioridad))
            ->action('Ver Ticket', url('/tickets/' . $this->ticket->id))
            ->line('Gracias por usar nuestro sistema de soporte.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'titulo' => $this->ticket->titulo,
            'mensaje' => 'Nuevo ticket creado: ' . $this->ticket->numero_ticket,
            'tipo' => 'creacion'
        ];
    }
}
