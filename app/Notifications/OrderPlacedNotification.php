<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->user->name ?? 'Guest',
            'amount' => $this->order->total_amount,
            'message' => 'New order #' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT) . ' has been placed.',
            'url' => route('admin.orders.show', $this->order->id)
        ];
    }
}
