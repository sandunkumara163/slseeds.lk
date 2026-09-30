@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-md w-full text-center">
        <div class="animate-pulse flex flex-col items-center">
            <div class="w-16 h-16 border-4 border-green-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Redirecting to Secure Payment...</h2>
            <p class="text-gray-500">Please wait while we transfer you to PayHere.</p>
        </div>

        <form id="payhereForm" method="POST" action="https://sandbox.payhere.lk/pay/checkout">   
            <input type="hidden" name="merchant_id" value="{{ $merchant_id }}">
            <input type="hidden" name="return_url" value="{{ route('payhere.return') }}">
            <input type="hidden" name="cancel_url" value="{{ route('payhere.cancel') }}">
            <input type="hidden" name="notify_url" value="{{ route('payhere.notify') }}">  
            
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="items" value="SLSeeds Order #{{ $order->id }}">
            <input type="hidden" name="currency" value="{{ $currency }}">
            <input type="hidden" name="amount" value="{{ $amount }}">  
            <input type="hidden" name="hash" value="{{ $hash }}">    

            <input type="hidden" name="first_name" value="{{ explode(' ', auth()->user()->name)[0] }}">
            <input type="hidden" name="last_name" value="{{ explode(' ', auth()->user()->name)[1] ?? '' }}">
            <input type="hidden" name="email" value="{{ auth()->user()->email }}">
            <input type="hidden" name="phone" value="{{ auth()->user()->phone ?? '0000000000' }}">
            <input type="hidden" name="address" value="{{ auth()->user()->address ?? 'N/A' }}">
            <input type="hidden" name="city" value="Colombo">
            <input type="hidden" name="country" value="Sri Lanka">
        </form> 
    </div>
</div>

<script>
    // Auto submit the form
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.getElementById('payhereForm').submit();
        }, 1500);
    });
</script>
@endsection
