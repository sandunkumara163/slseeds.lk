@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Customer Reviews</h1>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rating</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Review & Reply</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($reviews as $review)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="p-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $review->created_at->format('M d, Y') }}
                        </td>
                        <td class="p-4 text-sm text-gray-900 dark:text-white font-medium">
                            {{ $review->user->name }}
                        </td>
                        <td class="p-4 text-sm text-gray-600 dark:text-gray-300">
                            <a href="{{ route('products.show', $review->product_id) }}" class="text-green-600 hover:underline" target="_blank">
                                {{ $review->product->translation('en')->name ?? 'Product' }}
                            </a>
                        </td>
                        <td class="p-4 text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex text-yellow-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"><svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg></span>
                                @endfor
                            </div>
                        </td>
                        <td class="p-4 text-sm text-gray-600 dark:text-gray-300">
                            @if($review->comment)
                                <p class="mb-2 italic">"{{ Str::limit($review->comment, 60) }}"</p>
                            @else
                                <span class="text-gray-400 italic">No comment provided</span>
                            @endif

                            <div x-data="{ openReply: false }" class="mt-2">
                                @if($review->admin_reply)
                                    <div class="bg-green-50 dark:bg-gray-900 border border-green-200 dark:border-gray-700 p-2 rounded text-xs">
                                        <strong class="text-green-700 dark:text-green-500">Replied:</strong> {{ $review->admin_reply }}
                                    </div>
                                    <button @click="openReply = !openReply" class="text-xs text-blue-600 hover:underline mt-1">Edit Reply</button>
                                @else
                                    <button @click="openReply = !openReply" class="text-xs text-blue-600 hover:underline">Reply to Customer</button>
                                @endif

                                <div x-show="openReply" style="display: none;" class="mt-2">
                                    <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST" class="flex flex-col gap-2">
                                        @csrf
                                        <textarea name="admin_reply" rows="2" class="w-full text-sm border-gray-300 rounded focus:ring-green-500 bg-white dark:bg-gray-900" placeholder="Type your reply here..." required>{{ $review->admin_reply }}</textarea>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="openReply = false" class="text-xs text-gray-500 hover:underline">Cancel</button>
                                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-green-700">Save Reply</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-sm text-right whitespace-nowrap">
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)" class="text-red-500 hover:text-red-700 p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Delete Review">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">
                            No reviews found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reviews->hasPages())
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ $reviews->links() }}
        </div>
    @endif
</div>

<script>
    function confirmDelete(btn) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.closest('form').submit();
            }
        });
    }
</script>
@endsection
