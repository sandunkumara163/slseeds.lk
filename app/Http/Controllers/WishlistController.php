<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with(['product.translations'])->where('user_id', auth()->id())->get();
        return view('wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request, Product $product)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            $status = 'removed';
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id
            ]);
            $status = 'added';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => $status]);
        }

        return back()->with('success', $status === 'added' ? 'Product added to wishlist!' : 'Product removed from wishlist!');
    }
}
