<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        return back()->with('success', 'Thank you for your review!');
    }

    public function destroy(Review $review)
    {
        if (auth()->id() !== $review->user_id && auth()->user()->role !== 'admin') {
            abort(403);
        }
        
        $review->delete();
        return back()->with('success', 'Review deleted successfully.');
    }
}

