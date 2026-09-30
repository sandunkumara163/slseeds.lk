@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($cartItems->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="text-6xl mb-4">🛒</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-6">Looks like you haven't added any seeds to your cart yet.</p>
            <a href="{{ route('home') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full shadow-md transition duration-300">Start Shopping</a>
        </div>
    @else
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <ul class="divide-y divide-gray-100">
                        @foreach($cartItems as $item)
                            <li class="p-6 flex flex-col sm:flex-row items-center gap-6 hover:bg-gray-50 transition-colors">
                                <!-- Product Image -->
                                <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="Product" class="w-full h-full object-cover">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name=Seed+{{$item->product->id}}&background=d1fae5&color=065f46" alt="Product" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                
                                <!-- Product Details -->
                                <div class="flex-1 text-center sm:text-left">
                                    <a href="{{ route('products.show', $item->product->id) }}" class="text-lg font-bold text-gray-800 hover:text-green-600">{{ $item->product->translation('en')->name ?? 'Seed' }}</a>
                                    <p class="text-sm text-gray-500 mb-1">Unit Price: Rs. {{ $item->product->discount_price ?? $item->product->price }}</p>
                                    
                                    @if($item->product->stock_quantity > 0)
                                        <p class="text-xs text-green-600 font-semibold mb-2">Available: {{ $item->product->stock_quantity }} items</p>
                                        
                                        <!-- Quantity Update Form -->
                                        <form action="{{ route('cart.update') }}" method="POST" class="inline-flex items-center border border-gray-300 rounded-lg overflow-hidden h-8 w-28 bg-white">
                                            @csrf
                                            <input type="hidden" name="cart_id" value="{{ $item->id }}">
                                            <button type="button" onclick="if(this.nextElementSibling.value > 1) { this.nextElementSibling.stepDown(); this.form.submit(); }" class="w-8 h-full bg-gray-50 text-gray-600 hover:bg-gray-200 focus:outline-none font-bold text-lg">−</button>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}" class="w-12 h-full text-center border-none focus:ring-0 text-sm font-bold p-0 bg-white" onchange="if(this.value < 1) this.value = 1; if(this.value > {{ $item->product->stock_quantity }}) { this.value = {{ $item->product->stock_quantity }}; Swal.fire({icon: 'warning', title: 'Stock Limit', text: 'Only {{ $item->product->stock_quantity }} items available'}); } this.form.submit()">
                                            <button type="button" onclick="if(this.previousElementSibling.value < {{ $item->product->stock_quantity }}) { this.previousElementSibling.stepUp(); this.form.submit(); } else { Swal.fire({icon: 'warning', title: 'Stock Limit', text: 'Only {{ $item->product->stock_quantity }} items available'}) }" class="w-8 h-full bg-gray-50 text-gray-600 hover:bg-gray-200 focus:outline-none font-bold text-lg">+</button>
                                        </form>
                                    @else
                                        <p class="text-sm text-red-600 font-bold bg-red-50 inline-block px-3 py-1 rounded-full mt-2">Out of Stock</p>
                                    @endif
                                </div>
                                
                                <!-- Price & Remove -->
                                <div class="text-center sm:text-right flex flex-col justify-between h-full">
                                    <div class="text-lg font-bold text-green-700 mb-4">Rs. {{ ($item->product->discount_price ?? $item->product->price) * $item->quantity }}</div>
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="cart_id" value="{{ $item->id }}">
                                        <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium flex items-center justify-center sm:justify-end gap-1">
                                            <span>🗑️</span> Remove
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-4">Order Summary</h2>
                    
                    <div class="flex justify-between mb-4 text-gray-600">
                        <span>Subtotal ({{ $cartItems->count() }} items)</span>
                        <span class="font-medium">Rs. {{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-6 text-gray-600">
                        <span>Estimated Delivery</span>
                        <span class="font-medium text-green-600">Calculated at checkout</span>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 mb-6 flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Total</span>
                        <span class="text-2xl font-black text-green-700">Rs. {{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    <a href="{{ route('checkout.index') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-bold py-3 px-6 rounded-xl shadow-md transition duration-300">
                        Proceed to Checkout
                    </a>
                    
                    <div class="mt-4 flex items-center justify-center gap-2 text-sm text-gray-500">
                        <span>🔒</span> Secure Checkout
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
