<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$order = App\Models\Order::find(4);
if($order) {
    echo 'Order ID: ' . $order->id . "\n";
    echo 'Total Amount: ' . $order->total_amount . "\n";
    
    $merchant_id = '1238251';
    $merchant_secret = 'MTcxNTcyMjQ5MDg3NzQxNTIyNzczMzIzMjUzMTE2MjAwMjU5';
    $amount = number_format($order->total_amount, 2, '.', '');
    $currency = 'LKR';
    
    $hash = strtoupper(
        md5(
            $merchant_id . 
            $order->id . 
            $amount . 
            $currency .  
            strtoupper(md5($merchant_secret)) 
        ) 
    );
    echo "Amount formatted: " . $amount . "\n";
    echo "Calculated Hash: " . $hash . "\n";
} else {
    echo "Order not found\n";
}
