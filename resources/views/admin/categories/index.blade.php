@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors">
        + Add New Category
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">ID</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Name (EN)</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Slug</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Status</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($categories as $category)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="py-4 px-6 text-sm text-gray-700">{{ $category->id }}</td>
                <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $category->translation('en')->name ?? '-' }}</td>
                <td class="py-4 px-6 text-sm text-gray-500">{{ $category->slug }}</td>
                <td class="py-4 px-6 text-sm">
                    @if($category->status)
                        <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">Active</span>
                    @else
                        <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-semibold">Inactive</span>
                    @endif
                </td>
                <td class="py-4 px-6 text-sm text-right space-x-2">
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-blue-500 hover:text-blue-700 font-medium">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" id="delete-form-{{ $category->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete({{ $category->id }})" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
            
            @if($categories->isEmpty())
            <tr>
                <td colspan="5" class="py-8 text-center text-gray-500">No categories found.</td>
            </tr>
            @endif
        </tbody>
    </table>
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
