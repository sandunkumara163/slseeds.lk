@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">My Wishlist</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Products you have saved for later.</p>
    </div>

    @if($wishlists->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
            <div class="text-5xl mb-4">❤️</div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Your wishlist is empty</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Browse our products and save your favorites here.</p>
            <a href="{{ route('products.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition-colors inline-block">
                Start Shopping
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($wishlists as $wishlist)
                @php $product = $wishlist->product; @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full relative" id="wishlist-item-{{ $product->id }}">
                    <!-- Remove Button -->
                    <button type="button" onclick="removeFromWishlist({{ $product->id }})" class="absolute top-2 right-2 p-1.5 rounded-full bg-white/80 hover:bg-red-50 hover:text-red-500 text-gray-500 shadow-sm transition-colors z-10" title="Remove from Wishlist">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>

                    <a href="{{ route('products.show', $product->id) }}" class="block relative pb-[100%] bg-gray-50 dark:bg-gray-900 overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="Product" class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @else
                            <img src="https://ui-avatars.com/api/?name=Seed+{{$product->id}}&background=d1fae5&color=065f46&size=300" alt="Product" class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @endif
                    </a>
                    <div class="p-3 md:p-4 flex flex-col flex-grow">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm md:text-base leading-tight mb-2 line-clamp-2">
                            <a href="{{ route('products.show', $product->id) }}" class="hover:text-green-600 transition-colors">
                                {{ $product->translation(app()->getLocale())->name ?? 'Product' }}
                            </a>
                        </h3>
                        <div class="mt-auto">
                            @if($product->discount_price)
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-green-600 dark:text-green-400 text-sm md:text-lg">Rs. {{ number_format($product->discount_price, 2) }}</span>
                                    <span class="text-xs text-gray-400 line-through">Rs. {{ number_format($product->price, 2) }}</span>
                                </div>
                            @else
                                <div class="font-bold text-green-600 dark:text-green-400 text-sm md:text-lg">Rs. {{ number_format($product->price, 2) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function removeFromWishlist(productId) {
    fetch('/wishlist/' + productId + '/toggle', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'removed') {
            document.getElementById('wishlist-item-' + productId).remove();
            
            // Check if grid is empty
            const grid = document.querySelector('.grid');
            if (grid && grid.children.length === 0) {
                location.reload(); // Reload to show empty state
            }
        }
    });
}
</script>
@endpush

