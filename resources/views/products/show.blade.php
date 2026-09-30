@extends('layouts.customer')

@section('content')
<div class="bg-white dark:bg-gray-900 min-h-screen" x-data="{ quantity: 1, maxStock: {{ $product->stock_quantity }} }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex text-sm text-gray-500 dark:text-gray-400 mb-6 space-x-2">
            <a href="{{ route('home') }}" class="hover:text-green-600 dark:hover:text-green-400">Home</a>
            <span>/</span>
            <a href="#" class="hover:text-green-600 dark:hover:text-green-400">{{ $product->category->translation('en')->name ?? 'Category' }}</a>
            <span>/</span>
            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $product->translation('en')->name ?? 'Product' }}</span>
        </nav>

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Product Image -->
            <div class="w-full md:w-1/2 lg:w-2/5" x-data="{ 
    activeImage: '{{ $product->image ? asset('storage/' . $product->image) : 'https://ui-avatars.com/api/?name=Seed+'.$product->id.'&background=d1fae5&color=065f46&size=800' }}',
    zoomData: { x: 0, y: 0, show: false }
}">
    <!-- Main Image Container with Zoom -->
    <div 
        class="bg-gray-100 dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 shadow-sm relative pb-[100%] cursor-crosshair group"
        @mousemove="zoomData.show = true; let rect = $el.getBoundingClientRect(); zoomData.x = ($event.clientX - rect.left) / rect.width * 100; zoomData.y = ($event.clientY - rect.top) / rect.height * 100;"
        @mouseleave="zoomData.show = false"
    >
        <!-- The actual image -->
        <img :src="activeImage" alt="Product Image" 
            class="absolute inset-0 w-full h-full object-contain pointer-events-none transition-transform duration-100 ease-out"
            :class="zoomData.show ? 'scale-[2]' : 'scale-100'"
            :style="\`transform-origin: ${zoomData.x}% ${zoomData.y}%;\`">
            
        @if($product->discount_price)
            <div class="absolute top-4 left-4 bg-red-500 text-white font-bold px-3 py-1 rounded shadow-md z-30 pointer-events-none">SALE</div>
        @endif
        
        @auth
            @php $inWishlist = auth()->user()->wishlists->where('product_id', $product->id)->first(); @endphp
            <button type="button" onclick="event.preventDefault(); window.toggleWishlist({{ $product->id }}, this)" class="absolute top-4 right-4 p-2.5 rounded-full bg-white/80 hover:bg-white shadow-md transition-colors z-30 {{ $inWishlist ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                <svg class="w-6 h-6 {{ $inWishlist ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </button>
        @else
            <a href="{{ route('login') }}" class="absolute top-4 right-4 p-2.5 rounded-full bg-white/80 hover:bg-white shadow-md transition-colors z-30 text-gray-400 hover:text-red-500" title="Login to Add to Wishlist">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </a>
        @endauth
    </div>

    <!-- Thumbnails -->
    @if($product->image || $product->images->count() > 0)
        <div class="flex gap-2 mt-4 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-gray-300">
            @if($product->image)
                <button @click="activeImage = '{{ asset('storage/' . $product->image) }}'" :class="activeImage === '{{ asset('storage/' . $product->image) }}' ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200 dark:border-gray-700 opacity-70 hover:opacity-100'" class="flex-shrink-0 w-20 h-20 rounded-lg border-2 overflow-hidden bg-white transition-all">
                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                </button>
            @endif
            @foreach($product->images as $img)
                <button @click="activeImage = '{{ asset('storage/' . $img->image_path) }}'" :class="activeImage === '{{ asset('storage/' . $img->image_path) }}' ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200 dark:border-gray-700 opacity-70 hover:opacity-100'" class="flex-shrink-0 w-20 h-20 rounded-lg border-2 overflow-hidden bg-white transition-all">
                    <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
                </button>
            @endforeach
        </div>
    @endif
</div>

            <!-- Product Details -->
            <div class="w-full md:w-1/2 lg:w-3/5 flex flex-col">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $product->translation('en')->name ?? 'Seed Name' }}</h1>
                
                <!-- Ratings -->
<div class="flex items-center space-x-2 mb-4">
    <div class="flex text-yellow-400 text-sm">
        @php $avgRating = round($product->average_rating); @endphp
        @for($i = 1; $i <= 5; $i++)
            <span class="{{ $i <= $avgRating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"><svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg></span>
        @endfor
    </div>
    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $product->reviews->count() }} Reviews)</span>
</div>

                <!-- Price -->
                <div class="bg-green-50 dark:bg-gray-800 p-4 rounded-xl border border-green-100 dark:border-gray-700 mb-6">
                    @if($product->discount_price)
                        <div class="text-3xl font-black text-green-700 dark:text-green-400">Rs. {{ $product->discount_price }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 line-through mt-1">Rs. {{ $product->price }}</div>
                    @else
                        <div class="text-3xl font-black text-green-700 dark:text-green-400">Rs. {{ $product->price }}</div>
                    @endif
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 py-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</span>
                        <span class="text-xs font-semibold" :class="maxStock < 5 ? 'text-red-500' : 'text-green-600 dark:text-green-400'">
                            <span x-text="maxStock"></span> pieces available
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Quantity Selector -->
                        <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden h-10 w-32">
                            <button @click="if(quantity > 1) quantity--" class="w-10 h-full bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none flex items-center justify-center font-bold text-lg transition-colors">-</button>
                            <input type="number" x-model="quantity" readonly class="w-12 h-full text-center border-none focus:ring-0 text-sm font-bold p-0 bg-white dark:bg-gray-800 dark:text-white">
                            <button @click="if(quantity < maxStock) quantity++; else { Swal.fire({icon: 'warning', title: 'Stock Limit Reached', text: 'Only ' + maxStock + ' items available.'}) }" class="w-10 h-full bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none flex items-center justify-center font-bold text-lg transition-colors">+</button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 mt-auto pt-6">
                    <button @click="window.addToCart({{ $product->id }}, quantity)" class="flex-1 bg-green-50 dark:bg-gray-800 hover:bg-green-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-400 border border-green-600 dark:border-green-500 font-bold py-3 px-6 rounded-xl transition duration-300 flex items-center justify-center gap-2">
                        <span>🛒</span> Add to Cart
                    </button>
                    <button @click="window.addToCart({{ $product->id }}, quantity, true)" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition duration-300 flex items-center justify-center">Buy Now</button>

                        
                </div>
            </div>
        </div>
        
        <!-- Description Tabs -->
        <div class="mt-12 border-t border-gray-200 dark:border-gray-700 pt-8" x-data="{ tab: 'desc' }">
            <div class="flex space-x-6 border-b border-gray-200 dark:border-gray-700 mb-6">
                <button @click="tab = 'desc'" :class="tab == 'desc' ? 'border-green-600 text-green-600 dark:border-green-400 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="pb-3 border-b-2 font-medium transition-colors">Description</button>
                <button @click="tab = 'delivery'" :class="tab == 'delivery' ? 'border-green-600 text-green-600 dark:border-green-400 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="pb-3 border-b-2 font-medium transition-colors">Delivery Options</button>
<button @click="tab = 'reviews'" :class="tab == 'reviews' ? 'border-green-600 text-green-600 dark:border-green-400 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="pb-3 border-b-2 font-medium transition-colors">Reviews ({{ $product->reviews->count() }})</button>
            </div>
            
            <div x-show="tab === 'desc'" class="prose prose-sm sm:prose max-w-none text-gray-600 dark:text-gray-300">
                <p>{{ $product->translation('en')->description ?? 'No description available for this product.' }}</p>
            </div>
            
            <div x-show="tab === 'delivery'" class="text-sm text-gray-600 dark:text-gray-300" style="display: none;">
    <ul class="space-y-3">
        <li class="flex items-center gap-3"><span class="text-xl"><svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg></span> Standard Delivery (3-5 Days) - Rs. 350</li>
        <li class="flex items-center gap-3"><span class="text-xl"><svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></span> Cash on Delivery Available</li>
    </ul>
</div>

<div x-show="tab === 'reviews'" style="display: none;">
    @auth
    @php $myReview = $product->reviews->where('user_id', auth()->id())->first(); @endphp
    <form action="{{ route('reviews.store', $product->id) }}" method="POST" class="mb-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        @csrf
        <h4 class="font-bold text-gray-800 dark:text-white mb-2">{{ $myReview ? 'Edit Your Review' : 'Write a Review' }}</h4>
        <div class="mb-3">
                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Rating</label>
                <select name="rating" class="bg-white dark:bg-gray-900 border border-gray-300 rounded px-3 py-1.5 text-sm" required>
    <option value="5" {{ ($myReview && $myReview->rating == 5) ? 'selected' : '' }}>5 Stars - Excellent</option>
    <option value="4" {{ ($myReview && $myReview->rating == 4) ? 'selected' : '' }}>4 Stars - Good</option>
    <option value="3" {{ ($myReview && $myReview->rating == 3) ? 'selected' : '' }}>3 Stars - Average</option>
    <option value="2" {{ ($myReview && $myReview->rating == 2) ? 'selected' : '' }}>2 Stars - Poor</option>
    <option value="1" {{ ($myReview && $myReview->rating == 1) ? 'selected' : '' }}>1 Star - Terrible</option>
</select>
            </div>
            <div class="mb-3">
                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Comment (Optional)</label>
                <textarea name="comment" rows="3" class="w-full bg-white dark:bg-gray-900 border border-gray-300 rounded p-2 text-sm" placeholder="What did you like or dislike?">{{ $myReview->comment ?? '' }}</textarea>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded font-bold text-sm hover:bg-green-700">{{ $myReview ? 'Update Review' : 'Submit Review' }}</button>
        </form>
    @else
        <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-center">
            <p class="text-gray-600 dark:text-gray-400">Please <a href="{{ route('login') }}" class="text-green-600 hover:underline font-bold">login</a> to write a review.</p>
        </div>
    @endauth

    <div class="space-y-4">
        @forelse($product->reviews as $review)
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <div class="flex items-center justify-between mb-1">
    <span class="font-bold text-gray-800 dark:text-white text-sm">{{ $review->user->name }}</span>
    <div class="flex items-center space-x-3">
        <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
        @auth
            @if(auth()->id() == $review->user_id)
                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete your review?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold">Delete</button>
                </form>
            @endif
        @endauth
    </div>
</div>
                <div class="flex text-yellow-400 text-xs mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"><svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg></span>
                    @endfor
                </div>
                @if($review->comment)
    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $review->comment }}</p>
@endif
@if($review->admin_reply)
    <div class="mt-3 bg-gray-100 dark:bg-gray-900 p-3 rounded-lg border-l-4 border-green-500 text-sm text-gray-700 dark:text-gray-300">
        <strong class="block text-green-700 dark:text-green-500 mb-1">SLSeeds Reply:</strong>
        {{ $review->admin_reply }}
    </div>
@endif
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 italic text-sm">No reviews yet. Be the first to review this product!</p>
        @endforelse
    </div>
</div>
        </div>
    </div>
</div>
<!-- Related Products Section -->
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mt-16 pt-8 border-t border-gray-200 dark:border-gray-700 max-w-6xl mx-auto">
            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">You may also like</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-5">
                @foreach($relatedProducts as $related)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                        <a href="{{ route('products.show', $related->id) }}" class="block relative pb-[100%] bg-gray-50 dark:bg-gray-900 overflow-hidden">
                            @if($related->image)
                                <img src="{{ asset('storage/' . $related->image) }}" alt="Product" class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            @else
                                <img src="https://ui-avatars.com/api/?name=Seed+{{$related->id}}&background=d1fae5&color=065f46&size=300" alt="Product" class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            @endif
                        </a>
                        <div class="p-3 md:p-4 flex flex-col flex-grow">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm md:text-base leading-tight mb-2 line-clamp-2">{{ $related->translation(app()->getLocale())->name ?? 'Product' }}</h3>
                            <div class="mt-auto">
                                @if($related->discount_price)
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-green-600 dark:text-green-400 text-sm md:text-lg">Rs. {{ number_format($related->discount_price, 2) }}</span>
                                        <span class="text-xs text-gray-400 line-through">Rs. {{ number_format($related->price, 2) }}</span>
                                    </div>
                                @else
                                    <div class="font-bold text-green-600 dark:text-green-400 text-sm md:text-lg">Rs. {{ number_format($related->price, 2) }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection














