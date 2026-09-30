@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Dashboard Overview</h1>
    <div class="text-sm text-gray-500 dark:text-gray-400 bg-white px-3 py-1 rounded-full shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-2">
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
        </span>
        Live Updates Enabled
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 text-xl mr-4">
            📦
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">Pending Orders</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $pendingOrders }}</p>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-xl mr-4">
            💰
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">Rs. {{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-xl mr-4">
            ⚠️
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">Low Stock Items</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $lowStockCount }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl mr-4">
            👥
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">Customers</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalCustomers }}</p>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:col-span-2">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Revenue (Last 7 Days)</h3>
        <canvas id="revenueChart" height="100"></canvas>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Order Status Distribution</h3>
        <canvas id="statusChart" height="200"></canvas>
    </div>
</div>

<!-- Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Orders -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
            <h3 class="font-bold text-gray-800 dark:text-gray-100">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-green-600 hover:text-green-800 font-semibold">View All</a>
        </div>
        <div class="p-0">
            <table class="w-full text-left border-collapse">
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50 dark:bg-gray-900">
                        <td class="py-3 px-6 text-sm">
                            <span class="font-medium text-gray-800 dark:text-gray-100">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="py-3 px-6 text-sm">{{ $order->user->name ?? 'Guest' }}</td>
                        <td class="py-3 px-6 text-sm font-bold text-gray-700">Rs. {{ number_format($order->total_amount, 2) }}</td>
                        <td class="py-3 px-6 text-sm text-right">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase
                                @if($order->status == 'completed' || $order->status == 'delivered') bg-green-100 text-green-700
                                @elseif(in_array($order->status, ['refunded', 'cancelled', 'failed'])) bg-red-100 text-red-700
                                @elseif($order->status == 'pending') bg-yellow-100 text-yellow-700
                                @else bg-blue-100 text-blue-700 @endif
                            ">{{ $order->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-6 text-center text-gray-500 dark:text-gray-400">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
            <h3 class="font-bold text-gray-800 dark:text-gray-100">Low Stock Alerts</h3>
            <a href="{{ route('admin.stock.index') }}" class="text-sm text-green-600 hover:text-green-800 font-semibold">Manage Stock</a>
        </div>
        <div class="p-0">
            <table class="w-full text-left border-collapse">
                <tbody class="divide-y divide-gray-100">
                    @forelse($lowStockProducts as $product)
                    <tr class="hover:bg-gray-50 dark:bg-gray-900">
                        <td class="py-3 px-6 flex items-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-8 h-8 rounded object-cover mr-3">
                            @else
                                <div class="w-8 h-8 rounded bg-gray-200 mr-3"></div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 line-clamp-1">{{ $product->translation('en')->name ?? 'Product' }}</p>
                            </div>
                        </td>
                        <td class="py-3 px-6 text-sm text-right">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $product->stock_quantity <= 5 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $product->stock_quantity }} Left
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="p-6 text-center text-gray-500 dark:text-gray-400">All products are well stocked.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Revenue Bar Chart
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: {!! json_encode($last7Days) !!},
                datasets: [{
                    label: 'Revenue (Rs)',
                    data: {!! json_encode($revenueData) !!},
                    backgroundColor: 'rgba(22, 163, 74, 0.8)',
                    borderColor: 'rgb(21, 128, 61)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Status Pie Chart
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        const statusColors = {
            'pending': '#facc15',
            'processing': '#60a5fa',
            'shipped': '#818cf8',
            'delivered': '#4ade80',
            'completed': '#16a34a',
            'cancelled': '#f87171',
            'refunded': '#ef4444',
            'failed': '#dc2626',
            'on_hold': '#a78bfa'
        };
        
        const labels = {!! json_encode($statusLabels) !!};
        const data = {!! json_encode($statusData) !!};
        const colors = labels.map(label => statusColors[label] || '#9ca3af');

        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@endsection
