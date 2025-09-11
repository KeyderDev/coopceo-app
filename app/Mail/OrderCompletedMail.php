<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $cliente;

    public function __construct($order, $cliente)
    {
        $this->order = $order;
        $this->cliente = $cliente;
    }

    public function build()
    {
        return $this->subject('Resumen de su compra')
                    ->markdown('emails.orders.completed');
    }
}
