<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            abort(403, 'Admins cannot checkout.');
        }

        $cartItems = Cart::with(['product'])->where('user_id', auth()->id())->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->product->discount_price ?? $item->product->price;
            $subtotal += $price * $item->quantity;
        }
        
        $deliveryFee = 350; // Flat rate for now
        $total = $subtotal + $deliveryFee;

        return view('checkout.index', compact('cartItems', 'subtotal', 'deliveryFee', 'total'));
    }

    public function process(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            abort(403, 'Admins cannot checkout.');
        }

        $request->validate([
            'delivery_address' => 'required|string|max:500',
            'phone' => 'required|string|regex:/^0[0-9]{9}$/',
            'payment_method' => 'required|in:cod,payhere',
        ], [
            'phone.regex' => 'The phone number must be 10 digits starting with 0.',
        ]);

        $cartItems = Cart::with(['product'])->where('user_id', auth()->id())->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            $subtotal = 0;
            // 1. Stock Validation
            foreach ($cartItems as $item) {
                // Lock the row for update to prevent race conditions
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                
                if ($product->stock_quantity < $item->quantity) {
                    throw new \Exception("Sorry, only {$product->stock_quantity} of {$product->translation('en')->name} available.");
                }
                
                $price = $product->discount_price ?? $product->price;
                $subtotal += $price * $item->quantity;
            }

            $deliveryFee = 350;
            $total = $subtotal + $deliveryFee;

            // 2. Create Order
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => $total,
                'delivery_address' => $request->delivery_address . ' | Phone: ' . $request->phone,
                'status' => 'pending',
                'payment_method' => $request->payment_method
            ]);

            // 3. Deduct stock and create OrderItems
            foreach ($cartItems as $item) {
                $product = $item->product;
                $price = $product->discount_price ?? $product->price;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                ]);

                $product->decrement('stock_quantity', $item->quantity);
            }

            // 4. Clear Cart
            Cart::where('user_id', auth()->id())->delete();

            DB::commit();
            
                        if ($request->payment_method === 'payhere') {
                return redirect()->route('checkout.payhere', $order->id);
            }
            
            // 5. Fire Event & Notifications
            broadcast(new \App\Events\OrderPlaced($order));
            
            $admins = \App\Models\User::where('role', 'admin')->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\OrderPlacedNotification($order));

            foreach ($cartItems as $item) {
                if ($item->product->stock_quantity <= 5) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\LowStockNotification($item->product));
                }
            }

            return redirect()->route('checkout.success', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }

    public function payhere(Order $order)
    {
        if ($order->user_id !== auth()->id() || $order->status !== 'pending' || $order->payment_method !== 'payhere') {
            abort(403);
        }

        $merchant_id = config('services.payhere.merchant_id');
        $order_id = $order->id;
        $amount = number_format($order->total_amount, 2, '.', '');
        $currency = config('services.payhere.currency');
        $merchant_secret = config('services.payhere.secret');

        $hash = strtoupper(
            md5(
                $merchant_id . 
                $order_id . 
                $amount . 
                $currency .  
                strtoupper(md5($merchant_secret)) 
            ) 
        );

        return view('checkout.payhere', compact('order', 'merchant_id', 'amount', 'currency', 'hash'));
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('checkout.success', compact('order'));
    }
}
