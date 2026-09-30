@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700">&larr; Back to Orders</a>
        <h1 class="text-2xl font-bold text-gray-800">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
    </div>
    <a href="{{ route('admin.orders.receipt', $order->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition-colors flex items-center gap-2">
        <span>Download Receipt</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Details & Items -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Items Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800">Purchased Items</h2>
            </div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200">
                        <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase">Product</th>
                        <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase text-center">Price</th>
                        <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase text-center">Qty</th>
                        <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="py-4 px-6 text-sm font-bold text-gray-800">{{ $item->product->translation('en')->name ?? 'Seed' }}</td>
                        <td class="py-4 px-6 text-sm text-gray-600 text-center">Rs. {{ number_format($item->price, 2) }}</td>
                        <td class="py-4 px-6 text-sm font-bold text-center">{{ $item->quantity }}</td>
                        <td class="py-4 px-6 text-sm font-bold text-green-700 text-right">Rs. {{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="p-6 border-t border-gray-100 flex justify-end">
                <div class="w-1/2">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium text-gray-800">Rs. {{ number_format($order->total_amount - 350, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Delivery Fee</span>
                        <span class="font-medium text-gray-800">Rs. 350.00</span>
                    </div>
                    <div class="flex justify-between py-3 border-t border-gray-200 mt-2">
                        <span class="text-lg font-bold text-gray-800">Total</span>
                        <span class="text-xl font-black text-green-700">Rs. {{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Customer & Status -->
    <div class="space-y-6">
        
        <!-- Status Management -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Order Status</h2>
            
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        <option value="on_hold" {{ $order->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    Save Status
                </button>
            </form>
        </div>
        
        <!-- Customer Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Customer Details</h2>
            
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-gray-500 block">Name</span>
                    <span class="font-bold text-gray-800">{{ $order->user->name ?? 'Guest' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Email</span>
                    <a href="mailto:{{ $order->user->email ?? '' }}" class="text-blue-600 hover:underline">{{ $order->user->email ?? '-' }}</a>
                </div>
                <div>
                    <span class="text-gray-500 block">Delivery Address & Phone</span>
                    <span class="text-gray-800 whitespace-pre-wrap">{{ $order->delivery_address }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Payment Method</span>
                    <span class="font-bold text-gray-800 uppercase">{{ $order->payment_method }}</span>
                </div>
                @if($order->payment_method === 'payhere' && $order->transaction_id)
                <div>
                    <span class="text-gray-500 block">Payment No</span>
                    <span class="font-bold text-gray-800 uppercase">{{ $order->transaction_id }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
