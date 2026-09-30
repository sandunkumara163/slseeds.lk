@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            @if(request('search'))
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Search Results for') }} "{{ request('search') }}"</h1>
            @elseif(request('category'))
                @php $currentCat = $categories->firstWhere('id', request('category')); @endphp
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $currentCat ? $currentCat->translation(app()->getLocale())->name : 'Products' }}</h1>
            @else
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ __('All Products') }}</h1>
            @endif
        </div>
        
        <!-- Filters -->
<div class="flex space-x-2">
    <button type="button" onclick="document.getElementById('advancedFilters').classList.toggle('hidden')" class="bg-white dark:bg-gray-800 border border-gray-300 text-gray-700 dark:text-gray-200 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center gap-2 h-[42px]">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
        Filters
    </button>
    <form action="{{ route('products.index') }}" method="GET" id="sortForm">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
        @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
        @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif
        @if(request('in_stock')) <input type="hidden" name="in_stock" value="{{ request('in_stock') }}"> @endif
                
                <select name="sort" onchange="document.getElementById('sortForm').submit()" class="bg-white dark:bg-gray-800 border border-gray-300 text-gray-700 dark:text-gray-200 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">{{ __('Default Sorting') }}</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Category Filter Chips -->
    <div class="mb-8 overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-hide">
        <div class="flex space-x-2">
            <a href="{{ route('products.index', ['search' => request('search')]) }}" 
               class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-medium transition-colors border {{ !request('category') ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-600 hover:border-green-500 hover:text-green-600' }}">
                All Categories
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('products.index', ['category' => $cat->id, 'search' => request('search')]) }}" 
                   class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-medium transition-colors border {{ request('category') == $cat->id ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-600 hover:border-green-500 hover:text-green-600' }}">
                    {{ $cat->translation(app()->getLocale())->name ?? 'Category' }}
                </a>
            @endforeach
        </div>
</div>

<!-- Advanced Filters Panel -->
<div id="advancedFilters" class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 {{ request('min_price') || request('max_price') || request('in_stock') ? '' : 'hidden' }}">
    <form action="{{ route('products.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
        
        <div>
            <label class="block text-xs text-gray-500 mb-1">Min Price (Rs.)</label>
            <input type="number" name="min_price" value="{{ request('min_price') }}" class="w-24 bg-gray-50 border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Max Price (Rs.)</label>
            <input type="number" name="max_price" value="{{ request('max_price') }}" class="w-24 bg-gray-50 border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500">
        </div>
        <div class="flex items-center gap-2 mb-1">
            <input type="checkbox" name="in_stock" value="1" id="in_stock" {{ request('in_stock') ? 'checked' : '' }} class="text-green-600 focus:ring-green-500 rounded">
            <label for="in_stock" class="text-sm text-gray-700 dark:text-gray-200">In Stock Only</label>
        </div>
        
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded text-sm transition font-bold shadow-sm">Apply Filters</button>
        @if(request('min_price') || request('max_price') || request('in_stock'))
            <a href="{{ route('products.index', ['category' => request('category'), 'search' => request('search')]) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-1.5 rounded text-sm transition font-bold shadow-sm">Clear</a>
        @endif
    </form>
</div>

<!-- Product Grid -->
    @if($products->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
            <div class="text-5xl mb-4">🔍</div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ __('No products found') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">We couldn't find anything matching your search criteria.</p>
            <a href="{{ route('products.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition-colors inline-block">
                {{ __('Clear Filters') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($products as $product)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                    <a href="{{ route('products.show', $product->id) }}" class="block relative pb-[100%] bg-gray-50 dark:bg-gray-900 overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="Product" class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @else
                            <img src="https://ui-avatars.com/api/?name=Seed+{{$product->id}}&background=d1fae5&color=065f46&size=300" alt="Product" class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @endif
                        @if($product->discount_price)
    <div class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">SALE</div>
@endif
@auth
    @php $inWishlist = auth()->user()->wishlists->where('product_id', $product->id)->first(); @endphp
    <button type="button" onclick="event.preventDefault(); toggleWishlist({{ $product->id }}, this)" class="absolute top-2 right-2 p-1.5 rounded-full bg-white/80 hover:bg-white shadow-sm transition-colors z-10 {{ $inWishlist ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
        <svg class="w-5 h-5 {{ $inWishlist ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
    </button>
@endauth
                    </a>
                    <div class="p-3 md:p-4 flex flex-col flex-grow">
                        <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider font-semibold">{{ $product->category->translation(app()->getLocale())->name ?? 'Category' }}</p>
                        <a href="{{ route('products.show', $product->id) }}">
    <h3 class="text-sm md:text-base font-bold text-gray-800 dark:text-gray-100 mb-1 line-clamp-2 hover:text-green-600 transition-colors" title="{{ $product->translation(app()->getLocale())->name ?? 'Seed' }}">
        {{ $product->translation(app()->getLocale())->name ?? 'Seed Name' }}
    </h3>
</a>
<div class="flex items-center space-x-1 mb-1 text-xs">
    <div class="flex text-yellow-400">
        @php $avgRating = round($product->average_rating); @endphp
        @for($i = 1; $i <= 5; $i++)
            <span class="{{ $i <= $avgRating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"><svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg></span>
        @endfor
    </div>
    <span class="text-gray-400">({{ $product->reviews->count() }})</span>
</div>
                        
                        <div class="mt-auto pt-2">
                            <div class="flex items-center space-x-2">
                                @if($product->discount_price)
                                    <span class="text-green-600 font-bold text-sm md:text-base">{{ __('Rs.') }} {{ number_format($product->discount_price, 2) }}</span>
                                    <span class="text-xs text-gray-400 line-through">{{ __('Rs.') }} {{ number_format($product->price, 2) }}</span>
                                @else
                                    <span class="text-green-600 font-bold text-sm md:text-base">{{ __('Rs.') }} {{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                            <button onclick="addToCart({{$product->id}})" class="mt-3 w-full bg-green-50 hover:bg-green-600 text-green-700 hover:text-white font-semibold py-2 px-4 border border-green-200 hover:border-transparent rounded-lg transition-all duration-200 text-sm flex items-center justify-center gap-1">
                                <span>🛒</span> {{ __('Add') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection













