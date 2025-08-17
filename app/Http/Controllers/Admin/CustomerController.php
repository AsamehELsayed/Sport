<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = User::where('is_admin', false)
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('admin/customers/Index', [
            'customers' => $customers,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $customer)
    {
        if ($customer->is_admin) {
            abort(404);
        }

        $customer->load(['orders' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'orders.items.product']);

        return Inertia::render('admin/customers/Show', [
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $customer)
    {
        if ($customer->is_admin) {
            abort(404);
        }

        return Inertia::render('admin/customers/Edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $customer)
    {
        if ($customer->is_admin) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string',
            'marketing_emails' => 'boolean',
            'order_updates' => 'boolean',
            'preferred_sports' => 'nullable|array',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $customer)
    {
        if ($customer->is_admin) {
            abort(404);
        }

        if ($customer->orders()->count() > 0) {
            return back()->with('error', 'Cannot delete customer with associated orders.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'date_from', 'date_to']);

        return Excel::download(new \App\Exports\CustomersExport($filters), 'customers-report-' . date('Y-m-d') . '.xlsx');
    }
}
