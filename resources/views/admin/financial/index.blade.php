@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Financial Reports</h1>
    
    <div class="flex space-x-2">
        <a href="{{ route('admin.financial.export.pdf', ['filter' => $filter, 'method' => $method]) }}" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors text-sm">
            📄 Export PDF
        </a>
        <a href="{{ route('admin.financial.export.excel', ['filter' => $filter, 'method' => $method]) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors text-sm">
            📊 Export Excel (CSV)
        </a>
    </div>
</div>

<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
    <!-- Date Filter -->
    <div class="flex space-x-2 bg-gray-50 p-1 rounded-lg border border-gray-200">
        <a href="{{ route('admin.financial.index', ['filter' => 'all', 'method' => $method]) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ $filter == 'all' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-white' }}">All Time</a>
        <a href="{{ route('admin.financial.index', ['filter' => 'today', 'method' => $method]) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ $filter == 'today' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-white' }}">Today</a>
        <a href="{{ route('admin.financial.index', ['filter' => 'week', 'method' => $method]) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ $filter == 'week' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-white' }}">This Week</a>
        <a href="{{ route('admin.financial.index', ['filter' => 'month', 'method' => $method]) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ $filter == 'month' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-white' }}">This Month</a>
    </div>

    <!-- Method Filter -->
    <div class="flex space-x-2 bg-gray-50 p-1 rounded-lg border border-gray-200">
        <a href="{{ route('admin.financial.index', ['filter' => $filter, 'method' => 'all']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ $method == 'all' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-white' }}">All Methods</a>
        <a href="{{ route('admin.financial.index', ['filter' => $filter, 'method' => 'cod']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ $method == 'cod' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-white' }}">COD Only</a>
        <a href="{{ route('admin.financial.index', ['filter' => $filter, 'method' => 'payhere']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ $method == 'payhere' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-white' }}">Online Only</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Total Earned</h3>
        <p class="text-3xl font-bold text-green-600">Rs. {{ number_format($totalIncome, 2) }}</p>
        <p class="text-xs text-gray-400 mt-2">Net Income</p>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">COD Income</h3>
        <p class="text-2xl font-bold text-gray-800">Rs. {{ number_format($codIncome, 2) }}</p>
        <p class="text-xs text-gray-400 mt-2">Completed & Delivered</p>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Online (PayHere)</h3>
        <p class="text-2xl font-bold text-blue-600">Rs. {{ number_format($onlineIncome, 2) }}</p>
        <p class="text-xs text-gray-400 mt-2">Successful Payments</p>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-red-500 uppercase tracking-wider mb-2">Returns/Refunds</h3>
        <p class="text-xl font-bold text-red-600 mb-1">COD: Rs. {{ number_format($returnedCod, 2) }}</p>
        <p class="text-xl font-bold text-red-600">Online: Rs. {{ number_format($refundedOnline, 2) }}</p>
        <p class="text-xs text-gray-400 mt-2">Deducted from potential earnings</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Order ID</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Date</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Customer</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Method</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Status</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase text-right">Amount</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($orders as $order)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="py-4 px-6 text-sm text-gray-700 font-medium">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td class="py-4 px-6 text-sm text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</td>
                <td class="py-4 px-6 text-sm text-gray-700">{{ $order->user->name ?? 'Guest' }}</td>
                <td class="py-4 px-6 text-sm font-bold {{ $order->payment_method == 'payhere' ? 'text-blue-600' : 'text-gray-600' }} uppercase">
                    {{ $order->payment_method }}
                </td>
                <td class="py-4 px-6 text-sm">
                    <span class="px-2 py-1 rounded-full text-xs font-bold uppercase
                        @if($order->status == 'completed' || $order->status == 'delivered') bg-green-100 text-green-700
                        @elseif($order->status == 'refunded' || $order->status == 'cancelled' || $order->status == 'failed') bg-red-100 text-red-700
                        @else bg-blue-100 text-blue-700 @endif
                    ">{{ $order->status }}</span>
                </td>
                <td class="py-4 px-6 text-sm font-bold text-right @if($order->status == 'refunded' || $order->status == 'cancelled' || $order->status == 'failed') text-red-500 line-through @else text-gray-900 @endif">
                    Rs. {{ number_format($order->total_amount, 2) }}
                </td>
            </tr>
            @endforeach
            
            @if($orders->isEmpty())
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">No transactions found for this period.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
