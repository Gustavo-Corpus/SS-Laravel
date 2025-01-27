<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketActualizado extends Notification
{
    use Queueable;

    protected $ticket;
    protected $estadoAnterior;

    public function __construct(Ticket $ticket, $estadoAnterior)
    {
        $this->ticket = $ticket;
        $this->estadoAnterior = $estadoAnterior;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Actualización de Ticket: ' . $this->ticket->numero_ticket)
            ->line('El estado de su ticket ha sido actualizado.')
            ->line('Estado anterior: ' . ucfirst($this->estadoAnterior))
            ->line('Nuevo estado: ' . ucfirst($this->ticket->estado))
            ->action('Ver Ticket', url('/tickets/' . $this->ticket->id))
            ->line('Gracias por usar nuestro sistema de soporte.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'titulo' => $this->ticket->titulo,
            'mensaje' => 'Ticket ' . $this->ticket->numero_ticket . ' actualizado a: ' . ucfirst($this->ticket->estado),
            'tipo' => 'actualizacion'
        ];
    }
}
