<?php
$c = file_get_contents('routes/web.php');

$payhere_routes = <<<PHP
// PayHere Routes
Route::post('/payhere/notify', [\App\Http\Controllers\PayHereController::class, 'notify'])->name('payhere.notify');
Route::get('/payhere/return', [\App\Http\Controllers\PayHereController::class, 'returnPage'])->name('payhere.return');
Route::get('/payhere/cancel', [\App\Http\Controllers\PayHereController::class, 'cancelPage'])->name('payhere.cancel');

Route::middleware('auth')->group(function () {
PHP;

$c = str_replace("Route::middleware('auth')->group(function () {", $payhere_routes, $c);

// Also add checkout.payhere
$checkout_route = <<<PHP
    Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/{order}/payhere', [\App\Http\Controllers\CheckoutController::class, 'payhere'])->name('checkout.payhere');
PHP;

$c = str_replace("Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');", $checkout_route, $c);

file_put_contents('routes/web.php', $c);
