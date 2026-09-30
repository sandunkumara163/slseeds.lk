<?php
$c = file_get_contents('resources/views/checkout/index.blade.php');

$c = str_replace(
    '<input type="radio" name="payment_method" value="card" class="text-gray-400 focus:ring-0 h-5 w-5" disabled>',
    '<input type="radio" name="payment_method" value="payhere" class="text-green-600 focus:ring-green-500 h-5 w-5">',
    $c
);

$c = str_replace(
    '<label class="flex items-center p-4 border border-gray-200 bg-gray-50 rounded-lg cursor-not-allowed opacity-60 relative">',
    '<label class="flex items-center p-4 border border-gray-200 hover:border-green-500 bg-white hover:bg-green-50 rounded-lg cursor-pointer transition-colors relative" onclick="selectPayment(\'payhere\')">',
    $c
);

$c = str_replace(
    '<span class="ml-3 font-medium text-gray-600">Credit/Debit Card (Coming Soon)</span>',
    '<span class="ml-3 font-medium text-gray-900">Credit/Debit Card (PayHere)</span>',
    $c
);

// Add onclick handler to COD as well
$c = str_replace(
    '<label class="flex items-center p-4 border border-green-500 bg-green-50 rounded-lg cursor-pointer transition-colors relative">',
    '<label class="flex items-center p-4 border border-green-500 bg-green-50 rounded-lg cursor-pointer transition-colors relative" onclick="selectPayment(\'cod\')">',
    $c
);

$script = <<<HTML
<script>
    function selectPayment(method) {
        document.querySelector('input[value="cod"]').parentElement.className = 'flex items-center p-4 border border-gray-200 hover:border-green-500 bg-white hover:bg-green-50 rounded-lg cursor-pointer transition-colors relative';
        document.querySelector('input[value="payhere"]').parentElement.className = 'flex items-center p-4 border border-gray-200 hover:border-green-500 bg-white hover:bg-green-50 rounded-lg cursor-pointer transition-colors relative';
        
        document.querySelector('input[value="' + method + '"]').parentElement.className = 'flex items-center p-4 border border-green-500 bg-green-50 rounded-lg cursor-pointer transition-colors relative';
        document.querySelector('input[value="' + method + '"]').checked = true;
    }

    function confirmOrder() {
        let method = document.querySelector('input[name="payment_method"]:checked').value;
        let text = method === 'cod' ? "Are you sure you want to place this order with Cash on Delivery?" : "You will be redirected to PayHere to complete the payment securely.";
        
        Swal.fire({
            title: 'Confirm Order?',
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#d33',
            confirmButtonText: method === 'cod' ? 'Yes, Place Order' : 'Proceed to Pay'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('checkoutForm').submit();
            }
        })
    }
</script>
HTML;

$c = preg_replace('/<script>.*?<\/script>/s', $script, $c);

file_put_contents('resources/views/checkout/index.blade.php', $c);
