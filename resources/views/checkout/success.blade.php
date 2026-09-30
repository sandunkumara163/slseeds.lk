@extends('layouts.customer')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-10 relative overflow-hidden">
        <!-- Decorative bg -->
        <div class="absolute top-0 left-0 w-full h-2 bg-green-500"></div>
        
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="text-5xl">✅</span>
        </div>
        
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Order Confirmed!</h1>
        <p class="text-lg text-gray-500 mb-8">Thank you for your purchase. Your order has been successfully placed.</p>
        
        <div class="bg-gray-50 rounded-xl p-6 text-left mb-8 border border-gray-100">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Order ID</p>
                    <p class="text-lg font-bold text-gray-800">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Amount</p>
                    <p class="text-lg font-bold text-green-600">Rs. {{ number_format($order->total_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Payment Method</p>
                    <p class="text-base font-semibold text-gray-800 uppercase">{{ $order->payment_method }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Status</p>
                    <p class="text-base font-semibold text-orange-500 uppercase">{{ $order->status }}</p>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('home') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full shadow-md transition duration-300">
                Continue Shopping
            </a>
            <a href="{{ route('dashboard') }}" class="bg-white hover:bg-gray-50 text-gray-800 border border-gray-300 font-bold py-3 px-8 rounded-full shadow-sm transition duration-300">
                View Orders
            </a>
        </div>
    </div>
</div>
@endsection
