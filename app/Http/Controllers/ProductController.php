<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('translations')->where('status', 1)->get();
        
        $query = Product::with(['translations', 'category.translations', 'reviews'])->where('status', 'active');

        // Apply Category Filter
        if ($request->has('category') && $request->category !== '') {
            $query->where('category_id', $request->category);
        }

        // Apply Search Filter
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = $request->search;
            $query->whereHas('translations', function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // Apply Price Range Filter
        if ($request->has('min_price') && $request->min_price !== '') {
            $query->whereRaw('COALESCE(discount_price, price) >= ?', [$request->min_price]);
        }
        if ($request->has('max_price') && $request->max_price !== '') {
            $query->whereRaw('COALESCE(discount_price, price) <= ?', [$request->max_price]);
        }

        // Apply Stock Filter
        if ($request->has('in_stock') && $request->in_stock == '1') {
            $query->where('stock_quantity', '>', 0);
        }

        // Apply Sorting
        if ($request->has('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
            } elseif ($request->sort === 'price_desc') {
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        
        return view('products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::with(['translations', 'category.translations', 'reviews.user', 'images'])->findOrFail($id);
        
        $relatedProducts = Product::with('translations')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(5)
            ->get();
            
        return view('products.show', compact('product', 'relatedProducts'));
    }
}
