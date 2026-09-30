<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            abort(403, 'Admins cannot view cart.');
        }

        $cartItems = Cart::with(['product.translations'])->where('user_id', auth()->id())->get();
        
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->product->discount_price ?? $item->product->price;
            $subtotal += $price * $item->quantity;
        }

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    public function add(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return response()->json(['error' => 'Admins cannot place orders.'], 403);
        }
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if ($product->stock_quantity < $request->quantity) {
            return response()->json(['error' => "Sorry, only {$product->stock_quantity} items available."], 400);
        }

        $cart = Cart::where('user_id', auth()->id())
                    ->where('product_id', $request->product_id)
                    ->first();

        if ($cart) {
            $newQuantity = $cart->quantity + $request->quantity;
            if ($product->stock_quantity < $newQuantity) {
                return response()->json(['error' => "Cannot exceed available stock."], 400);
            }
            $cart->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['success' => 'Product added to cart']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('user_id', auth()->id())->findOrFail($request->cart_id);
        
        if ($cart->product->stock_quantity < $request->quantity) {
            return back()->with('error', "Sorry, only {$cart->product->stock_quantity} items available.");
        }

        $cart->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Cart updated');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
        ]);

        Cart::where('user_id', auth()->id())->where('id', $request->cart_id)->delete();
        return back()->with('success', 'Item removed');
    }
}
