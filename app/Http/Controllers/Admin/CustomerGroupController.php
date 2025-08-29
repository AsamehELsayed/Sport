<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerGroupController extends Controller
{
    /**
     * Display a listing of customer groups.
     */
    public function index()
    {
        $groups = CustomerGroup::withCount('customers')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('admin/customer-groups/Index', [
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new customer group.
     */
    public function create()
    {
        return Inertia::render('admin/customer-groups/Create');
    }

    /**
     * Store a newly created customer group.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:customer_groups',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7|regex:/^#[0-9A-F]{6}$/i',
        ]);

        CustomerGroup::create($validated);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', 'Customer group created successfully.');
    }

    /**
     * Display the specified customer group.
     */
    public function show(CustomerGroup $customerGroup)
    {
        $customerGroup->load(['customers' => function ($query) {
            $query->withCount('orders')
                ->withSum('orders', 'total')
                ->orderBy('created_at', 'desc');
        }]);
        $availableCustomers = User::where('is_admin', false)
            ->whereDoesntHave('customerGroups', function ($query) use ($customerGroup) {
                $query->where('customer_group_id', $customerGroup->id);
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
        return Inertia::render('admin/customer-groups/Show', [
            'group' => $customerGroup,
            'availableCustomers' => $availableCustomers,
        ]);
    }

    /**
     * Show the form for editing the specified customer group.
     */
    public function edit(CustomerGroup $customerGroup)
    {
        return Inertia::render('admin/customer-groups/Edit', [
            'group' => $customerGroup,
        ]);
    }

    /**
     * Update the specified customer group.
     */
    public function update(Request $request, CustomerGroup $customerGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:customer_groups,name,' . $customerGroup->id,
            'description' => 'nullable|string',
            'color' => 'required|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'is_active' => 'boolean',
        ]);

        $customerGroup->update($validated);

        return redirect()->route('admin.customer-groups.show', $customerGroup)
            ->with('success', 'Customer group updated successfully.');
    }

    /**
     * Remove the specified customer group.
     */
    public function destroy(CustomerGroup $customerGroup)
    {
        if ($customerGroup->customers()->count() > 0) {
            return back()->with('error', 'Cannot delete group with associated customers.');
        }

        $customerGroup->delete();

        return redirect()->route('admin.customer-groups.index')
            ->with('success', 'Customer group deleted successfully.');
    }

    /**
     * Add customers to a group.
     */
    public function addCustomers(Request $request, CustomerGroup $customerGroup)
    {
        $validated = $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:users,id',
        ]);

        $customerGroup->customers()->attach($validated['customer_ids']);

        return back()->with('success', 'Customers added to group successfully.');
    }

    /**
     * Remove customers from a group.
     */
    public function removeCustomers(Request $request, CustomerGroup $customerGroup)
    {
        $validated = $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:users,id',
        ]);

        $customerGroup->customers()->detach($validated['customer_ids']);

        return back()->with('success', 'Customers removed from group successfully.');
    }

    /**
     * Get customers not in the group for selection.
     */
    public function getAvailableCustomers(CustomerGroup $customerGroup)
    {
        $customers = User::where('is_admin', false)
            ->whereDoesntHave('customerGroups', function ($query) use ($customerGroup) {
                $query->where('customer_group_id', $customerGroup->id);
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json($customers);
    }
}
