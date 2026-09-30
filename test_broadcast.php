<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $msg = App\Models\Message::first();
    broadcast(new App\Events\MessageSent($msg));
    echo "Broadcast success\n";
} catch (\Exception $e) {
    echo "Broadcast failed: " . $e->getMessage() . "\n";
}
