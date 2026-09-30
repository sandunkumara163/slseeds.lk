<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductTranslation;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
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
        
        $products = $query->paginate(15)->appends($request->query());
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::with('translations')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'name_en' => 'required|string',
            'description_en' => 'nullable|string',
            'name_si' => 'nullable|string',
            'name_ta' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'category_id' => $request->category_id,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock_quantity' => $request->stock_quantity,
            'status' => $request->has('status') ? 'active' : 'inactive',
            'image' => $imagePath,
        ]);

                $this->syncTranslations($request, $product);

        if ($request->hasFile('image_gallery')) {
            foreach ($request->file('image_gallery') as $image) {
                $path = $image->store('products/gallery', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $product->load('images');
        $categories = Category::with('translations')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'name_en' => 'required|string',
            'description_en' => 'nullable|string',
            'name_si' => 'nullable|string',
            'name_ta' => 'nullable|string',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'category_id' => $request->category_id,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock_quantity' => $request->stock_quantity,
            'status' => $request->has('status') ? 'active' : 'inactive',
            'image' => $imagePath,
        ]);

                $this->syncTranslations($request, $product);

        if ($request->hasFile('image_gallery')) {
            foreach ($request->file('image_gallery') as $image) {
                $path = $image->store('products/gallery', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        try {
            foreach ($product->images as $img) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($img->image_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image_path);
                }
            }
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
            $product->delete();
            return back()->with('success', 'Product deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return back()->with('error', 'Cannot delete this product because it has already been ordered by customers. Please set its status to "Inactive" instead to hide it from the store.');
            }
            return back()->with('error', 'An error occurred while deleting the product.');
        }
    }

    private function syncTranslations(Request $request, Product $product)
    {
        $langs = ['en', 'si', 'ta'];
        foreach ($langs as $lang) {
            $nameField = "name_{$lang}";
            $descField = "description_{$lang}";
            
            if ($request->filled($nameField)) {
                ProductTranslation::updateOrCreate(
                    ['product_id' => $product->id, 'language_code' => $lang],
                    ['name' => $request->$nameField, 'description' => $request->$descField]
                );
            } else {
                ProductTranslation::where('product_id', $product->id)->where('language_code', $lang)->delete();
            }
        }
    }

    public function deleteImage(ProductImage $image)
    {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();
        return back()->with('success', 'Image deleted successfully!');
    }
}