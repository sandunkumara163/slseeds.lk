<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Product;

class LowStockNotification extends Notification
{
    use Queueable;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->translation('en')->name ?? 'Product',
            'stock_quantity' => $this->product->stock_quantity,
            'message' => 'Low stock alert! Only ' . $this->product->stock_quantity . ' left for ' . ($this->product->translation('en')->name ?? 'Product') . '.',
            'url' => route('admin.stock.index')
        ];
    }
}
