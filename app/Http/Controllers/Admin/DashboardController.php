<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Top Cards Data
        $pendingOrders = Order::where('status', 'pending')->count();
        
        $totalRevenue = Order::where(function($query) {
            $query->where('payment_method', 'cod')->whereIn('status', ['delivered', 'completed'])
                  ->orWhere('payment_method', 'payhere')->whereIn('status', ['processing', 'shipped', 'delivered', 'completed']);
        })->sum('total_amount');
        
        $lowStockCount = Product::where('stock_quantity', '<=', 10)->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // 2. Bar Chart Data (Revenue last 7 days)
        $last7Days = collect();
        $revenueData = collect();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days->push($date->format('M d'));
            
            $dayRevenue = Order::whereDate('created_at', $date)
                ->where(function($query) {
                    $query->where('payment_method', 'cod')->whereIn('status', ['delivered', 'completed'])
                          ->orWhere('payment_method', 'payhere')->whereIn('status', ['processing', 'shipped', 'delivered', 'completed']);
                })->sum('total_amount');
                
            $revenueData->push($dayRevenue);
        }

        // 3. Pie Chart Data (Order Status Distribution)
        $orderStatuses = Order::select('status', DB::raw('count(*) as total'))
                              ->groupBy('status')
                              ->pluck('total', 'status')->toArray();
                              
        $statusLabels = array_keys($orderStatuses);
        $statusData = array_values($orderStatuses);

        // 4. Recent Orders List
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        
        // 5. Low Stock Products List
        $lowStockProducts = Product::with('translations')->where('stock_quantity', '<=', 10)->orderBy('stock_quantity')->take(5)->get();

        return view('admin.dashboard', compact(
            'pendingOrders', 'totalRevenue', 'lowStockCount', 'totalCustomers',
            'last7Days', 'revenueData', 'statusLabels', 'statusData',
            'recentOrders', 'lowStockProducts'
        ));
    }
}
