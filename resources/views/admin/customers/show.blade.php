@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center space-x-4">
    <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-700">← Back to Customers</a>
    <h1 class="text-2xl font-bold text-gray-800">Customer Details</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Customer Profile -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="w-24 h-24 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <h2 class="text-xl font-bold text-gray-800">{{ $customer->name }}</h2>
            <p class="text-gray-500 mb-6">{{ $customer->email }}</p>
            
            <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-4">
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Orders</span>
                    <span class="text-lg font-bold text-gray-800">{{ $customer->orders->count() }}</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Spent</span>
                    <span class="text-lg font-bold text-green-600">Rs. {{ number_format($totalSpent, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 border-b pb-2">Contact Info</h3>
            <div class="space-y-4">
                <div>
                    <span class="block text-xs text-gray-500 mb-1">Phone Number</span>
                    <span class="text-gray-800">{{ $customer->phone ?? 'Not provided' }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500 mb-1">Default Address</span>
                    <span class="text-gray-800">{{ $customer->address ?? 'Not provided' }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500 mb-1">Joined Date</span>
                    <span class="text-gray-800">{{ $customer->created_at->format('F d, Y') }}</span>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold py-2 px-4 rounded transition-colors border border-gray-200">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Order History -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Order History</h2>
            </div>
            
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200">
                        <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase">Order ID</th>
                        <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase">Date</th>
                        <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase text-center">Status</th>
                        <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase text-right">Total</th>
                        <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($customer->orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6 text-sm font-medium text-gray-900">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-4 px-6 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="py-4 px-6 text-sm text-center">
                            @if($order->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">Pending</span>
                            @elseif($order->status === 'completed')
                                <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">Completed</span>
                            @elseif($order->status === 'cancelled')
                                <span class="bg-gray-100 text-gray-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">Cancelled</span>
                            @else
                                <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold uppercase">{{ $order->status }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-sm font-bold text-green-700 text-right">Rs. {{ number_format($order->total_amount, 2) }}</td>
                        <td class="py-4 px-6 text-sm text-right">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-500 hover:text-blue-700 font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                    
                    @if($customer->orders->isEmpty())
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">No orders found for this customer.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
