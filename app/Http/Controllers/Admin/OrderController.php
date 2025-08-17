<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to']),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items']);

        // Ensure accessor attributes are included
        $order->append([
            'status_label',
            'payment_status_label',
            'formatted_total',
            'formatted_subtotal',
            'formatted_tax',
            'formatted_shipping',
            'formatted_original_subtotal',
            'formatted_total_discount',
            'has_discounts',
            'total_discount',
            'original_subtotal'
        ]);

        return Inertia::render('admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function edit(Order $order)
    {
        $order->load(['user', 'items']);

        // Ensure accessor attributes are included
        $order->append([
            'status_label',
            'payment_status_label',
            'formatted_total',
            'formatted_subtotal',
            'formatted_tax',
            'formatted_shipping',
            'formatted_original_subtotal',
            'formatted_total_discount',
            'has_discounts',
            'total_discount',
            'original_subtotal'
        ]);

        return Inertia::render('admin/Orders/Edit', [
            'order' => $order,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'admin_notes' => 'nullable|string',
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'admin_notes' => $request->admin_notes,
        ]);

        // Update timestamps based on status
        if ($request->status === 'processing' && !$order->processed_at) {
            $order->update(['processed_at' => now()]);
        }

        if ($request->status === 'shipped' && !$order->shipped_at) {
            $order->update(['shipped_at' => now()]);
        }

        if ($request->status === 'delivered' && !$order->delivered_at) {
            $order->update(['delivered_at' => now()]);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully!');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully!');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();

        foreach ($orders as $order) {
            $order->update(['status' => $request->status]);

            // Update timestamps based on status
            if ($request->status === 'processing' && !$order->processed_at) {
                $order->update(['processed_at' => now()]);
            }

            if ($request->status === 'shipped' && !$order->shipped_at) {
                $order->update(['shipped_at' => now()]);
            }

            if ($request->status === 'delivered' && !$order->delivered_at) {
                $order->update(['delivered_at' => now()]);
            }
        }

        return back()->with('success', count($orders) . ' orders updated successfully!');
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'status', 'date_from', 'date_to']);

        return Excel::download(new \App\Exports\OrdersExport($filters), 'orders-report-' . date('Y-m-d') . '.xlsx');
    }

    public function generateInvoice(Order $order)
    {
        return redirect()->route('admin.invoices.create', $order);
    }
}
