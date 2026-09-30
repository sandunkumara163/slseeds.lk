@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700">&larr; Back</a>
        <h1 class="text-2xl font-bold text-gray-800">Add Manual Order</h1>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-4xl" x-data="manualOrderForm()">
    <form action="{{ route('admin.orders.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Customer Section -->
            <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h3 class="font-bold text-gray-700 mb-2 border-b pb-2">Customer Details</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Type *</label>
                    <select name="customer_type" x-model="customerType" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                        <option value="existing">Select Existing Customer</option>
                        <option value="new">Add New (Guest) Customer</option>
                    </select>
                </div>
                
                <div x-show="customerType === 'existing'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Customer *</label>
                    <select name="user_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" :required="customerType === 'existing'">
                        <option value="">-- Choose Existing Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? 'No Phone' }})</option>
                        @endforeach
                    </select>
                    @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div x-show="customerType === 'new'" class="space-y-4" style="display: none;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                        <input type="text" name="new_name" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="Kamal Perera" :required="customerType === 'new'">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                        <input type="text" name="new_phone" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="07XXXXXXXX" :required="customerType === 'new'">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address *</label>
                    <textarea name="delivery_address" rows="3" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required placeholder="House No, Street, City, District"></textarea>
                    @error('delivery_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Payment & Status Section -->
            <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h3 class="font-bold text-gray-700 mb-2 border-b pb-2">Order Details</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                    <select name="payment_method" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                        <option value="cod">Cash on Delivery (COD)</option>
                        <option value="bank_transfer">Bank Transfer (Manual)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Initial Status *</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" required>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed (Direct Sale)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="font-bold text-gray-800 text-lg">Products *</h3>
                <button type="button" @click="addRow" class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200 font-semibold">+ Add Product</button>
            </div>
            
            <template x-for="(row, index) in rows" :key="row.id">
                <div class="flex items-center gap-4 mb-4 bg-white p-3 border border-gray-100 rounded-lg shadow-sm">
                    <div class="flex-grow">
                        <select :name="'products['+index+'][id]'" x-model="row.productId" @change="updatePrice(index)" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->discount_price ?? $product->price }}" data-stock="{{ $product->stock_quantity }}">
                                    {{ $product->translation('en')->name ?? 'Unknown' }} (Rs. {{ $product->discount_price ?? $product->price }}) - {{ $product->stock_quantity }} in stock
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="w-32">
                        <input type="number" :name="'products['+index+'][quantity]'" x-model="row.quantity" @input="calculateTotal" min="1" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Qty" required>
                    </div>

                    <div class="w-32 text-right font-bold text-gray-700">
                        Rs. <span x-text="row.total.toFixed(2)"></span>
                    </div>
                    
                    <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded">
                        🗑️
                    </button>
                </div>
            </template>
        </div>

        <!-- Totals -->
        <div class="flex justify-end mb-6">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 w-full md:w-1/3">
                <div class="flex justify-between mb-2 text-sm">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-bold">Rs. <span x-text="subtotal.toFixed(2)"></span></span>
                </div>
                <div class="flex justify-between mb-2 text-sm">
                    <span class="text-gray-600">Delivery Fee:</span>
                    <span class="font-bold">Rs. 350.00</span>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                    <span class="text-gray-800 font-bold">Total:</span>
                    <span class="font-bold text-xl text-green-600">Rs. <span x-text="(subtotal + 350).toFixed(2)"></span></span>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg shadow-md transition-colors text-lg">
                Create Order
            </button>
        </div>
    </form>
</div>

<script>
    function manualOrderForm() {
        return {
            customerType: 'existing',
            rows: [
                { id: Date.now(), productId: '', quantity: 1, price: 0, total: 0 }
            ],
            subtotal: 0,
            
            addRow() {
                this.rows.push({
                    id: Date.now(),
                    productId: '',
                    quantity: 1,
                    price: 0,
                    total: 0
                });
            },
            
            removeRow(index) {
                if (this.rows.length > 1) {
                    this.rows.splice(index, 1);
                    this.calculateTotal();
                }
            },
            
            updatePrice(index) {
                const select = event.target;
                const option = select.options[select.selectedIndex];
                if (option.value) {
                    this.rows[index].price = parseFloat(option.getAttribute('data-price'));
                } else {
                    this.rows[index].price = 0;
                }
                this.calculateTotal();
            },
            
            calculateTotal() {
                this.subtotal = 0;
                this.rows.forEach(row => {
                    const qty = parseInt(row.quantity) || 0;
                    row.total = row.price * qty;
                    this.subtotal += row.total;
                });
            }
        }
    }
</script>
@endsection
