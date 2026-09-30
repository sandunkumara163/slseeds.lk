@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center space-x-4">
    <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700">← Back</a>
    <h1 class="text-2xl font-bold text-gray-800">Create Product</h1>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Basic Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Basic Details & English Translation</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images (Optional)</label>
                        <input type="file" name="image_gallery[]" multiple accept="image/jpeg,image/png,image/webp" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <p class="text-xs text-gray-500 mt-1">You can select multiple images for the product gallery.</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Name (EN) *</label>
                        <input type="text" name="name_en" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (EN)</label>
                        <textarea name="description_en" rows="4" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->translation('en')->name ?? $category->slug }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Other Translations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="{ tab: 'si' }">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Other Translations</h3>
                
                <div class="flex space-x-4 mb-4">
                    <button type="button" @click="tab = 'si'" :class="tab == 'si' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors">Sinhala (සිංහල)</button>
                    <button type="button" @click="tab = 'ta'" :class="tab == 'ta' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors">Tamil (தமிழ்)</button>
                </div>
                
                <div x-show="tab === 'si'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Name (SI)</label>
                        <input type="text" name="name_si" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (SI)</label>
                        <textarea name="description_si" rows="3" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"></textarea>
                    </div>
                </div>
                
                <div x-show="tab === 'ta'" class="space-y-4" style="display: none;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Name (TA)</label>
                        <input type="text" name="name_ta" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (TA)</label>
                        <textarea name="description_ta" rows="3" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Options -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Pricing & Inventory</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Regular Price (Rs.) *</label>
                        <input type="number" step="0.01" name="price" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Price (Rs.)</label>
                        <input type="number" step="0.01" name="discount_price" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                        <input type="number" name="stock_quantity" value="0" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Visibility</h3>
                
                <div class="flex items-center mb-6">
                    <input type="checkbox" name="status" id="status" class="rounded text-green-600 focus:ring-green-500 w-5 h-5" checked>
                    <label for="status" class="ml-2 text-sm font-medium text-gray-700">Active (Visible to customers)</label>
                </div>
                
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition-colors">
                    Save Product
                </button>
            </div>
        </div>
    </div>
</form>
@endsection



