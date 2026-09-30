@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Order Management</h1>
    <a href="{{ route('admin.orders.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors text-sm">
        + Add Manual Order
    </a>
</div>

<!-- Controls Row: Tabs & Filters -->
<div class="mb-6 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm p-1 inline-flex space-x-1 border border-gray-200 overflow-x-auto max-w-full">
        @php
            $tabs = [
                'all' => 'All Orders',
                'pending' => 'Pending',
                'processing' => 'Processing',
                'shipped' => 'Shipped',
                'delivered' => 'Delivered',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'refunded' => 'Refunded',
                'on_hold' => 'On Hold',
                'failed' => 'Failed'
            ];
        @endphp

        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.orders.index', ['status' => $key, 'date' => request('date')]) }}" 
               class="whitespace-nowrap px-4 py-2 text-sm font-medium rounded-md transition-colors
               {{ $currentStatus === $key ? 'bg-green-600 text-white shadow' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Date Filter & Export -->
    <div class="flex items-center gap-2">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="date" name="date" value="{{ request('date') }}" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-green-500 focus:border-green-500" onchange="this.form.submit()">
            @if(request('date'))
                <a href="{{ route('admin.orders.index', ['status' => request('status')]) }}" class="text-gray-500 hover:text-red-500 text-xs font-semibold mr-2">Clear</a>
            @endif
        </form>

        <a href="{{ route('admin.orders.exportCsv', ['status' => request('status'), 'date' => request('date')]) }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-1.5 px-3 rounded-lg shadow-sm transition-colors text-sm flex items-center">
            <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            CSV
        </a>
        
        <a href="{{ route('admin.orders.exportPdf', ['status' => request('status'), 'date' => request('date')]) }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-1.5 px-3 rounded-lg shadow-sm transition-colors text-sm flex items-center">
            <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            PDF
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Order ID</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Customer</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Date</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Total</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase text-center">Status</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($orders as $order)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="py-4 px-6 text-sm font-bold text-gray-900">
                    #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                    @if($order->is_manual)
                        <span class="ml-2 bg-purple-100 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Manual</span>
                    @endif
                </td>
                <td class="py-4 px-6 text-sm text-gray-700">
                    {{ $order->user->name ?? 'Guest' }}<br>
                    <span class="text-xs text-gray-500">{{ $order->user->email ?? '' }}</span>
                </td>
                <td class="py-4 px-6 text-sm text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</td>
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
                <td class="py-4 px-6 text-sm text-right">
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-500 hover:text-blue-700 font-bold bg-blue-50 px-3 py-1 rounded transition-colors">View Details</a>
                </td>
            </tr>
            @endforeach
            
            @if($orders->isEmpty())
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">No orders found.</td>
            </tr>
            @endif
        </tbody>
    </table>
    
    <div class="p-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
</div>
@endsection

