@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Registered Customers</h1>
    <a href="{{ route('admin.customers.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors text-sm">
        + Add Customer
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Customer</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Email</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase">Joined Date</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase text-center">Total Orders</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase text-right">Total Spent</th>
                <th class="py-3 px-6 text-sm font-bold text-gray-600 uppercase text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($customers as $customer)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="py-4 px-6 text-sm font-bold text-gray-900">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-100 text-green-700 rounded-full flex items-center justify-center font-bold">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        {{ $customer->name }}
                    </div>
                </td>
                <td class="py-4 px-6 text-sm text-gray-600">{{ $customer->email }}</td>
                <td class="py-4 px-6 text-sm text-gray-500">{{ $customer->created_at->format('M d, Y') }}</td>
                <td class="py-4 px-6 text-sm font-bold text-gray-700 text-center">
                    <span class="bg-gray-100 px-3 py-1 rounded-full">{{ $customer->orders_count }}</span>
                </td>
                <td class="py-4 px-6 text-sm font-bold text-green-700 text-right">
                    Rs. {{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}
                </td>
                <td class="py-4 px-6 text-sm text-right space-x-2">
                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-blue-500 hover:text-blue-700 font-medium">View</a>
                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="text-yellow-600 hover:text-yellow-800 font-medium">Edit</a>
                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="inline" id="delete-form-{{ $customer->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete({{ $customer->id }})" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
            
            @if($customers->isEmpty())
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">No customers found.</td>
            </tr>
            @endif
        </tbody>
    </table>
    
    <div class="p-4 border-t border-gray-100">
        {{ $customers->links() }}
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Delete Customer?',
            text: "This will also delete all orders associated with this customer. You won't be able to revert this!",
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
