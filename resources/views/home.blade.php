@extends('layouts.customer')

@section('content')
    <!-- Hero Banner -->
    <div class="bg-green-50 dark:bg-green-900 py-8 lg:py-16 border-b border-green-100 dark:border-green-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-5xl font-extrabold text-green-800 dark:text-green-100 mb-4 tracking-tight">Grow Your Own Fresh Food</h1>
            <p class="text-base md:text-lg text-green-700 dark:text-green-200 mb-8 max-w-2xl mx-auto">Premium quality vegetable, fruit, and flower seeds delivered right to your doorstep in Sri Lanka.</p>
            <a href="{{ route('products.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full shadow-md transition duration-300">{{ __('Shop Now') }}</a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" id="products">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Featured Seeds</h2>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-green-600 hover:text-green-700">View All →</a>
        </div>
        
        <!-- Product Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($products as $product)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                    <a href="{{ route('products.show', $product->id) }}" class="block relative pb-[100%] bg-gray-50 overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="Product" class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @else
                            <img src="https://ui-avatars.com/api/?name=Seed+{{$product->id}}&background=d1fae5&color=065f46&size=300" alt="Product" class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @endif
                        @if($product->discount_price)
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">SALE</div>
                        @endif
                    </a>
                    <div class="p-3 md:p-4 flex flex-col flex-grow">
                        <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider font-semibold">{{ $product->category->translation(app()->getLocale())->name ?? 'Category' }}</p>
                        <a href="{{ route('products.show', $product->id) }}">
                            <h3 class="text-sm md:text-base font-bold text-gray-800 dark:text-gray-100 mb-1 line-clamp-2 hover:text-green-600 transition-colors" title="{{ $product->translation(app()->getLocale())->name ?? 'Seed' }}">
                                {{ $product->translation(app()->getLocale())->name ?? 'Seed Name' }}
                            </h3>
                        </a>
                        
                        <div class="mt-auto pt-2">
                            <div class="flex items-center space-x-2">
                                @if($product->discount_price)
                                    <span class="text-green-600 font-bold text-sm md:text-base">{{ __('Rs.') }} {{ $product->discount_price }}</span>
                                    <span class="text-xs text-gray-400 line-through">{{ __('Rs.') }} {{ $product->price }}</span>
                                @else
                                    <span class="text-green-600 font-bold text-sm md:text-base">{{ __('Rs.') }} {{ $product->price }}</span>
                                @endif
                            </div>
                            <button onclick="addToCart({{$product->id}})" class="mt-3 w-full bg-green-50 dark:bg-green-900 hover:bg-green-600 text-green-700 hover:text-white font-semibold py-2 px-4 border border-green-200 hover:border-transparent rounded-lg transition-all duration-200 text-sm flex items-center justify-center gap-1">
                                <span>🛒</span> Add
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
@endsection
