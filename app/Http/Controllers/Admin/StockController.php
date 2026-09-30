<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Product::with(['translations', 'category.translations']);
        
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->whereHas('translations', function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $products = $query->paginate(20)->appends($request->query());
        return view('admin.stock.index', compact('products'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0'
        ]);

        $product->update([
            'stock_quantity' => $request->stock_quantity
        ]);

        return back()->with('success', 'Stock updated successfully!');
    }
}
