<?php
$file = 'resources/views/admin/products/index.blade.php';
$content = file_get_contents($file);

$searchForm = <<<EOF
<div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
    <h1 class="text-2xl font-bold text-gray-800">Products</h1>
    
    <div class="flex items-center gap-4 w-full sm:w-auto">
        <form action="{{ route('admin.products.index') }}" method="GET" class="relative w-full sm:w-64">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full rounded-lg border-gray-300 pr-10 focus:border-green-500 focus:ring-green-500">
            <button type="submit" class="absolute right-0 top-0 bottom-0 px-3 text-gray-500 hover:text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>
        
        <a href="{{ route('admin.products.create') }}" class="whitespace-nowrap bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors">
            + Add New Product
        </a>
    </div>
</div>
EOF;

$content = preg_replace('/<div class="mb-6 flex justify-between items-center">.*?<\/div>/s', $searchForm, $content, 1);
file_put_contents($file, $content);

$file2 = 'resources/views/admin/stock/index.blade.php';
$content2 = file_get_contents($file2);

$searchForm2 = <<<EOF
<div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
    <h1 class="text-2xl font-bold text-gray-800">Stock Management</h1>
    
    <form action="{{ route('admin.stock.index') }}" method="GET" class="relative w-full sm:w-64">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full rounded-lg border-gray-300 pr-10 focus:border-green-500 focus:ring-green-500">
        <button type="submit" class="absolute right-0 top-0 bottom-0 px-3 text-gray-500 hover:text-green-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </button>
    </form>
</div>
EOF;

$content2 = preg_replace('/<div class="mb-6 flex justify-between items-center">.*?<\/div>/s', $searchForm2, $content2, 1);
file_put_contents($file2, $content2);

echo "Done\n";
?>
