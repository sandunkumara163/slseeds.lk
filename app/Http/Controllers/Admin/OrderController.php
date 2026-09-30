<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::with('translations')->where('status', 'active')->where('stock_quantity', '>', 0)->get();
        return view('admin.orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_type' => 'required|in:existing,new',
            'user_id' => 'required_if:customer_type,existing',
            'new_name' => 'required_if:customer_type,new|string',
            'new_phone' => 'required_if:customer_type,new|string|regex:/^0[0-9]{9}$/',
            'delivery_address' => 'required|string',
            'payment_method' => 'required|in:cod,payhere,bank_transfer',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'status' => 'required|in:pending,processing,shipped,delivered,completed'
        ], [
            'new_phone.regex' => 'The phone number must be 10 digits starting with 0.',
        ]);

        try {
            DB::beginTransaction();

            $userId = $request->user_id;

            if ($request->customer_type === 'new') {
                $user = User::create([
                    'name' => $request->new_name,
                    'email' => 'guest_' . time() . '@slseeds.lk',
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(10)),
                    'phone' => $request->new_phone,
                    'address' => $request->delivery_address,
                    'role' => 'customer'
                ]);
                $userId = $user->id;
            }

            $total = 0;
            $items = [];
            
            // Validate stock and calculate total
            foreach ($request->products as $productData) {
                if (empty($productData['id']) || empty($productData['quantity'])) continue;
                
                $product = Product::findOrFail($productData['id']);
                
                if ($product->stock_quantity < $productData['quantity']) {
                    throw new \Exception("Not enough stock for {$product->slug}. Only {$product->stock_quantity} left.");
                }

                $price = $product->discount_price ?? $product->price;
                $total += $price * $productData['quantity'];
                
                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'price' => $price,
                ];
            }

            if (empty($items)) {
                throw new \Exception("No valid products selected.");
            }

            // Calculate delivery fee
            $deliveryFee = 350; // default delivery fee
            $totalAmount = $total + $deliveryFee;

            // Create Order
            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => $totalAmount,
                'delivery_address' => $request->delivery_address,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'is_manual' => true
            ]);

            // Create Order Items and reduce stock
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                Product::where('id', $item['product_id'])->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();
            
            // Only broadcast if status changed to something else, or if just created. 
            // We'll skip broadcast for manual orders to avoid customer confusion, or we can broadcast.
            // Let's broadcast it so it updates realtime.
            broadcast(new \App\Events\OrderStatusUpdated($order));

            return redirect()->route('admin.orders.index')->with('success', 'Manual order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $query = Order::with(['user'])->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->paginate(15)->withQueryString();
        
        $currentStatus = $request->status ?? 'all';
        $currentDate = $request->date;
        
        return view('admin.orders.index', compact('orders', 'currentStatus', 'currentDate'));
    }

    public function exportCsv(Request $request)
    {
        $query = Order::with(['user', 'items.product.translations'])->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=orders_export.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Order ID', 'Date', 'Customer Name', 'Customer Email', 'Phone', 'Address', 'Products', 'Total', 'Payment Method', 'Status'];

        $callback = function() use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $products = $order->items->map(function($item) {
                    $productName = 'Unknown';
                    if ($item->product && $item->product->translations) {
                        $translation = $item->product->translations->where('language_code', 'en')->first();
                        if ($translation) $productName = $translation->name;
                    }
                    return $productName . ' (x' . $item->quantity . ')';
                })->implode(', ');
                
                $customerName = $order->user ? $order->user->name : 'Guest';
                $customerEmail = $order->user ? $order->user->email : 'N/A';
                
                // Parse delivery_address if it contains phone
                $address = $order->delivery_address ?? ($order->user ? $order->user->address : 'N/A');
                $phone = $order->user ? $order->user->phone : 'N/A';
                
                if (strpos($address, '| Phone:') !== false) {
                    $parts = explode('| Phone:', $address);
                    $address = trim($parts[0]);
                    $phone = trim($parts[1]);
                }

                fputcsv($file, [
                    '#' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    $order->created_at->format('Y-m-d H:i'),
                    $customerName,
                    $customerEmail,
                    $phone,
                    $address,
                    $products,
                    'Rs. ' . number_format($order->total_amount, 2),
                    ucfirst($order->payment_method),
                    strtoupper($order->status)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Order::with(['user', 'items.product.translations'])->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->get();
        $date = $request->date ?? 'All Dates';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.pdf', compact('orders', 'date'));
        // Set paper to landscape for better table viewing
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('orders_export.pdf');
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product.translations']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled,refunded,on_hold,failed'
        ]);

        $order->update(['status' => $request->status]);
        
        broadcast(new \App\Events\OrderStatusUpdated($order));

        return back()->with('success', 'Order status updated successfully!');
    }
}
