<?php
$file = 'resources/views/products/show.blade.php';
$content = file_get_contents($file);

$search = '/<!-- Main Image Container with Zoom -->.*?<!-- Thumbnails -->/s';

$replace = <<<EOF
<!-- Main Image Container with Zoom -->
    <div 
        class="bg-gray-100 dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 shadow-sm relative pb-[100%] cursor-crosshair group"
        @mousemove="zoomData.show = true; zoomData.x = \$event.offsetX / \$event.target.offsetWidth * 100; zoomData.y = \$event.offsetY / \$event.target.offsetHeight * 100;"
        @mouseleave="zoomData.show = false"
    >
        <!-- The actual image -->
        <img :src="activeImage" alt="Product Image" 
            class="absolute inset-0 w-full h-full object-contain pointer-events-none transition-transform duration-100 ease-out"
            :class="zoomData.show ? 'scale-[2]' : 'scale-100'"
            :style="\`transform-origin: \${zoomData.x}% \${zoomData.y}%;\`">
            
        @if(\$product->discount_price)
            <div class="absolute top-4 left-4 bg-red-500 text-white font-bold px-3 py-1 rounded shadow-md z-30 pointer-events-none">SALE</div>
        @endif
        
        @auth
            @php \$inWishlist = auth()->user()->wishlists->where('product_id', \$product->id)->first(); @endphp
            <button type="button" onclick="event.preventDefault(); window.toggleWishlist({{ \$product->id }}, this)" class="absolute top-4 right-4 p-2.5 rounded-full bg-white/80 hover:bg-white shadow-md transition-colors z-30 {{ \$inWishlist ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                <svg class="w-6 h-6 {{ \$inWishlist ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </button>
        @else
            <a href="{{ route('login') }}" class="absolute top-4 right-4 p-2.5 rounded-full bg-white/80 hover:bg-white shadow-md transition-colors z-30 text-gray-400 hover:text-red-500" title="Login to Add to Wishlist">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </a>
        @endauth
    </div>

    <!-- Thumbnails -->
EOF;

$content = preg_replace($search, $replace, $content, 1);
file_put_contents($file, $content);
echo "Done\n";
?>
