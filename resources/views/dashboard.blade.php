@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Account</h1>
            <p class="text-gray-500">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <div class="mt-4 md:mt-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold py-2 px-6 rounded-full shadow-sm transition duration-300">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Order History</h2>
        </div>
        
        @if($orders->isEmpty())
            <div class="p-10 text-center">
                <div class="text-4xl mb-3">📦</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No orders yet</h3>
                <p class="text-gray-500 mb-6">You haven't placed any orders yet. Start shopping to see your orders here.</p>
                <a href="{{ route('home') }}" class="text-green-600 font-bold hover:underline">Browse Seeds &rarr;</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-200">
                            <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Order ID</th>
                            <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Date</th>
                            <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Items</th>
                            <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Total</th>
                            <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase text-center">Status</th>
                            <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="py-4 px-6 text-sm text-gray-700">
                                <ul class="list-disc pl-4">
                                    @foreach($order->items as $item)
                                        <li>{{ $item->product->translation('en')->name ?? 'Seed' }} (x{{ $item->quantity }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 px-6 text-sm font-bold text-green-700">Rs. {{ number_format($order->total_amount, 2) }}</td>
                            <td class="py-4 px-6 text-sm text-center">
                                @if($order->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">Pending</span>
                                @elseif($order->status === 'processing')
                                    <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">Processing</span>
                                @elseif($order->status === 'shipped')
                                    <span class="bg-indigo-100 text-indigo-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">Shipped</span>
                                @elseif($order->status === 'delivered')
                                    <span class="bg-teal-100 text-teal-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">Delivered</span>
                                @elseif($order->status === 'completed')
                                    <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">Completed</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="bg-gray-100 text-gray-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">Cancelled</span>
                                @elseif($order->status === 'refunded')
                                    <span class="bg-purple-100 text-purple-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">Refunded</span>
                                @elseif($order->status === 'on_hold')
                                    <span class="bg-orange-100 text-orange-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">On Hold</span>
                                @else
                                    <span class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">{{ str_replace('_', ' ', $order->status) }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-right space-x-2">
                                <a href="{{ route('orders.receipt', $order->id) }}" class="text-blue-500 hover:text-blue-700 font-bold border border-blue-200 hover:border-blue-500 rounded px-3 py-1 transition-colors text-xs mr-1">
                                    Receipt
                                </a>
                                @if($order->status === 'pending')
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="inline" id="cancel-form-{{ $order->id }}">
                                        @csrf
                                        <button type="button" onclick="confirmCancel({{ $order->id }})" class="text-red-500 hover:text-red-700 font-bold border border-red-200 hover:border-red-500 rounded px-3 py-1 transition-colors text-xs">
                                            Cancel Order
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
    function confirmCancel(orderId) {
        Swal.fire({
            title: 'Cancel Order?',
            text: "Are you sure you want to cancel this order? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Yes, Cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form-' + orderId).submit();
            }
        });
    }
</script>
@endsection
