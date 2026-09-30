<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|regex:/^0[0-9]{9}$/',
            'address' => 'nullable|string',
            'password' => 'required|string|min:8',
        ], [
            'phone.regex' => 'The phone number must be 10 digits starting with 0.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'customer'
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer added successfully!');
    }

    public function show(User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        $customer->load(['orders' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'orders.items.product.translations']);
        
        $totalSpent = $customer->orders()->sum('total_amount');

        return view('admin.customers.show', compact('customer', 'totalSpent'));
    }

    public function edit(User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|regex:/^0[0-9]{9}$/',
            'address' => 'nullable|string',
        ], [
            'phone.regex' => 'The phone number must be 10 digits starting with 0.',
        ]);

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully!');
    }

    public function destroy(User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        // Delete related orders (assuming cascaded foreign keys, but let's be safe)
        foreach ($customer->orders as $order) {
            $order->items()->delete();
            $order->delete();
        }
        
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully!');
    }
}
