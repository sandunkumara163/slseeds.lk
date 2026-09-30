<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'product.translations'])->latest()->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function reply(Request $request, Review $review)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000'
        ]);

        $review->update([
            'admin_reply' => $request->admin_reply
        ]);

        return back()->with('success', 'Reply added successfully.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted successfully.');
    }
}
