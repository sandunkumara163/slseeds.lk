<?php
$file = 'resources/views/products/show.blade.php';
$content = file_get_contents($file);

$search = '/<div class="md:col-span-1">\s*<div class="relative bg-white.*?<\/div>\s*<\/div>/s';
$replace = <<<EOF
<div class="md:col-span-1" x-data="{ 
    activeImage: '{{ \$product->image ? asset('storage/' . \$product->image) : 'https://ui-avatars.com/api/?name=Seed+'.\$product->id.'&background=d1fae5&color=065f46&size=800' }}',
    zoomData: { x: 0, y: 0, show: false }
}">
    <!-- Main Image Container with Zoom -->
    <div 
        class="relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 pb-[100%] cursor-crosshair group"
        @mousemove="zoomData.show = true; zoomData.x = \$event.offsetX / \$event.target.offsetWidth * 100; zoomData.y = \$event.offsetY / \$event.target.offsetHeight * 100;"
        @mouseleave="zoomData.show = false"
    >
        <img :src="activeImage" alt="Product Image" class="absolute inset-0 w-full h-full object-contain pointer-events-none">
        
        <!-- Zoom Lens Overlay -->
        <div 
            x-show="zoomData.show" 
            class="absolute inset-0 pointer-events-none z-20"
            :style="\`background-image: url('\${activeImage}'); background-position: \${zoomData.x}% \${zoomData.y}%; background-size: 200%; background-repeat: no-repeat; background-color: white;\`"
            style="display: none;"
        ></div>

        @if(\$product->discount_price)
            <div class="absolute top-4 left-4 bg-red-500 text-white font-bold px-3 py-1 rounded shadow-md z-30 pointer-events-none">SALE</div>
        @endif
        
        @auth
            @php \$inWishlist = auth()->user()->wishlists->where('product_id', \$product->id)->first(); @endphp
            <button type="button" onclick="event.preventDefault(); window.toggleWishlist({{ \$product->id }}, this)" class="absolute top-4 right-4 p-2 rounded-full bg-white hover:bg-gray-50 shadow-md transition-colors z-30 {{ \$inWishlist ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                <svg class="w-6 h-6 {{ \$inWishlist ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </button>
        @endauth
    </div>

    <!-- Thumbnails -->
    @if(\$product->image || \$product->images->count() > 0)
        <div class="flex gap-2 mt-4 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-gray-300">
            @if(\$product->image)
                <button @click="activeImage = '{{ asset('storage/' . \$product->image) }}'" :class="activeImage === '{{ asset('storage/' . \$product->image) }}' ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200 dark:border-gray-700 opacity-70 hover:opacity-100'" class="flex-shrink-0 w-20 h-20 rounded-lg border-2 overflow-hidden bg-white transition-all">
                    <img src="{{ asset('storage/' . \$product->image) }}" class="w-full h-full object-cover">
                </button>
            @endif
            @foreach(\$product->images as \$img)
                <button @click="activeImage = '{{ asset('storage/' . \$img->image_path) }}'" :class="activeImage === '{{ asset('storage/' . \$img->image_path) }}' ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200 dark:border-gray-700 opacity-70 hover:opacity-100'" class="flex-shrink-0 w-20 h-20 rounded-lg border-2 overflow-hidden bg-white transition-all">
                    <img src="{{ asset('storage/' . \$img->image_path) }}" class="w-full h-full object-cover">
                </button>
            @endforeach
        </div>
    @endif
</div>
EOF;
$content = preg_replace($search, $replace, $content, 1);
file_put_contents($file, $content);
echo "Done\n";
?>
