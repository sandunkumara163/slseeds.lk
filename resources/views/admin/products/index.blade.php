@extends('layouts.admin')

@section('content')
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

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Product</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Category</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Price</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Stock</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Status</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($products as $product)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name=Seed+{{$product->id}}&background=d1fae5&color=065f46" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $product->translation('en')->name ?? 'Seed' }}</span>
                    </div>
                </td>
                <td class="py-4 px-6 text-sm text-gray-600">{{ $product->category->translation('en')->name ?? '-' }}</td>
                <td class="py-4 px-6 text-sm text-gray-600">
                    @if($product->discount_price)
                        <span class="text-green-600 font-bold">Rs. {{ $product->discount_price }}</span><br>
                        <span class="text-xs line-through text-gray-400">Rs. {{ $product->price }}</span>
                    @else
                        Rs. {{ $product->price }}
                    @endif
                </td>
                <td class="py-4 px-6 text-sm">
                    @if($product->stock_quantity > 10)
                        <span class="text-green-600 font-bold">{{ $product->stock_quantity }}</span>
                    @elseif($product->stock_quantity > 0)
                        <span class="text-orange-500 font-bold">{{ $product->stock_quantity }}</span>
                    @else
                        <span class="text-red-500 font-bold bg-red-50 px-2 py-1 rounded">Out of Stock</span>
                    @endif
                </td>
                <td class="py-4 px-6 text-sm">
                    @if($product->status === 'active')
                        <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">Active</span>
                    @else
                        <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-semibold">Inactive</span>
                    @endif
                </td>
                <td class="py-4 px-6 text-sm text-right space-x-2">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-500 hover:text-blue-700 font-medium">Edit</a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" id="delete-form-{{ $product->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete({{ $product->id }})" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
            
            @if($products->isEmpty())
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">No products found.</td>
            </tr>
            @endif
        </tbody>
    </table>
    
    <div class="p-4 border-t border-gray-100">
        {{ $products->links() }}
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
