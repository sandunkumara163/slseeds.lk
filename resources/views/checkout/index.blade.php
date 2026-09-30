@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Column: Shipping & Payment -->
            <div class="w-full lg:w-2/3 space-y-6">
                
                <!-- Shipping Address -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span>📍</span> Shipping Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" value="{{ auth()->user()->name }}" class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 cursor-not-allowed focus:ring-0" readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="text" name="phone" value="{{ auth()->user()->phone }}" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required placeholder="07XXXXXXXX">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" value="{{ auth()->user()->email }}" class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 cursor-not-allowed focus:ring-0" readonly>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address *</label>
                            <textarea name="delivery_address" rows="3" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required placeholder="House No, Street, City, District">{{ auth()->user()->address }}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span>💳</span> Payment Method
                    </h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-green-500 bg-green-50 rounded-lg cursor-pointer transition-colors relative" onclick="selectPayment('cod')">
                            <input type="radio" name="payment_method" value="cod" class="text-green-600 focus:ring-green-500 h-5 w-5" checked>
                            <span class="ml-3 font-medium text-green-900">Cash on Delivery (COD)</span>
                            <span class="absolute right-4 text-green-600 font-bold">Rs. {{ number_format($total, 2) }}</span>
                        </label>
                        
                        <label class="flex items-center p-4 border border-gray-200 hover:border-green-500 bg-white hover:bg-green-50 rounded-lg cursor-pointer transition-colors relative" onclick="selectPayment('payhere')">
                            <input type="radio" name="payment_method" value="payhere" class="text-green-600 focus:ring-green-500 h-5 w-5">
                            <span class="ml-3 font-medium text-gray-900">Credit/Debit Card (PayHere)</span>
                            <div class="absolute right-4 flex space-x-1">
                                <span class="bg-blue-600 text-white text-[10px] px-1 rounded">VISA</span>
                                <span class="bg-orange-500 text-white text-[10px] px-1 rounded">MC</span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>
            
            <!-- Right Column: Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Your Order</h2>
                    
                    <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2">
                        @foreach($cartItems as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name=Seed+{{$item->product->id}}&background=d1fae5&color=065f46" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 line-clamp-1">{{ $item->product->translation('en')->name ?? 'Seed' }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-800">Rs. {{ ($item->product->discount_price ?? $item->product->price) * $item->quantity }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>Subtotal</span>
                            <span class="font-medium">Rs. {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>Delivery Fee</span>
                            <span class="font-medium">Rs. {{ number_format($deliveryFee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                            <span class="text-lg font-bold text-gray-800">Total</span>
                            <span class="text-2xl font-black text-green-700">Rs. {{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                    
                    <button type="button" onclick="confirmOrder()" class="w-full bg-green-600 hover:bg-green-700 text-white text-center font-bold py-3 px-6 rounded-xl shadow-md transition duration-300 text-lg">
                        Place Order
                    </button>
                    
                    <p class="text-xs text-center text-gray-400 mt-4">By placing this order you agree to our Terms & Conditions.</p>
                </div>
            </div>
        </div>
    </form>
</div>

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
@endsection
