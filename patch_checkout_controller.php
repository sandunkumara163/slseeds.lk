<?php
$c = file_get_contents('app/Http/Controllers/CheckoutController.php');

// Change validation rule
$c = str_replace(
    "'payment_method' => 'required|in:cod',",
    "'payment_method' => 'required|in:cod,payhere',",
    $c
);

// After DB::commit(); add payhere logic
$payhere_logic = <<<PHP
            if (\$request->payment_method === 'payhere') {
                return redirect()->route('checkout.payhere', \$order->id);
            }
            
            // 5. Fire Event
PHP;
$c = str_replace("// 5. Fire Event", $payhere_logic, $c);

file_put_contents('app/Http/Controllers/CheckoutController.php', $c);
