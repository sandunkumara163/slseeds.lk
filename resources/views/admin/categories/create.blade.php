@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center space-x-4">
    <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700">← Back</a>
    <h1 class="text-2xl font-bold text-gray-800">Create Category</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category Slug</label>
                <input type="text" name="slug" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="e.g. vegetable-seeds" required>
            </div>
            
            <div class="flex items-center mt-6">
                <input type="checkbox" name="status" id="status" class="rounded text-green-600 focus:ring-green-500 w-5 h-5" checked>
                <label for="status" class="ml-2 text-sm font-medium text-gray-700">Active (Visible to customers)</label>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Translations</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (English) *</label>
                    <input type="text" name="name_en" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Sinhala)</label>
                    <input type="text" name="name_si" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Tamil)</label>
                    <input type="text" name="name_ta" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg shadow-sm transition-colors">
                Save Category
            </button>
        </div>
    </form>
</div>
@endsection
