<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Exports\OrdersExport;
use App\Exports\CustomersExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['order.user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($orderQuery) use ($search) {
                      $orderQuery->where('customer_name', 'like', "%{$search}%")
                                ->orWhere('customer_email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('admin/invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to']),
        ]);
    }

    public function create($order_id)
    {
        $order = Order::with(['user', 'items'])->findOrFail($order_id);

        return Inertia::render('admin/invoices/Create', [
            'order' => $order,
        ]);
    }

    public function store(Request $request, $order_id)
    {
        $request->validate([
            'due_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
        ]);

        $order = Order::findOrFail($order_id);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_date' => now(),
            'due_date' => $request->due_date,
            'status' => 'draft',
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
            'shipping' => $order->shipping,
            'discount' => $order->total_discount,
            'total' => $order->total,
            'notes' => $request->notes,
            'terms_conditions' => $request->terms_conditions,
        ]);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice created successfully!');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.items']);

        return Inertia::render('admin/invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.items']);

        return Inertia::render('admin/invoices/Edit', [
            'invoice' => $invoice,
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string',
        ]);

        $data = $request->only([
            'status', 'due_date', 'notes', 'terms_conditions',
            'payment_method', 'payment_reference'
        ]);

        // Update timestamps based on status
        if ($request->status === 'sent' && $invoice->status !== 'sent') {
            $data['sent_at'] = now();
        }

        if ($request->status === 'paid' && $invoice->status !== 'paid') {
            $data['paid_at'] = now();
        }

        $invoice->update($data);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully!');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully!');
    }

    public function generatePdf(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.items']);

        $pdf = PDF::loadView('admin.invoices.pdf', [
            'invoice' => $invoice,
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Helvetica',
        ]);

        $filename = "invoice-{$invoice->invoice_number}.pdf";

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($pdf->output()),
        ]);
    }

    public function previewPdf(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.items']);

        $pdf = PDF::loadView('admin.invoices.pdf', [
            'invoice' => $invoice,
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Helvetica',
        ]);

        $filename = "invoice-{$invoice->invoice_number}.pdf";

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Content-Length' => strlen($pdf->output()),
        ]);
    }

    public function sendInvoice(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Here you would typically send an email with the invoice
        // For now, we'll just update the status

        return back()->with('success', 'Invoice marked as sent!');
    }

    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Invoice marked as paid!');
    }

    public function exportOrders(Request $request)
    {
        $filters = $request->only(['search', 'status', 'date_from', 'date_to']);

        return Excel::download(new OrdersExport($filters), 'orders-report-' . date('Y-m-d') . '.xlsx');
    }

    public function exportCustomers(Request $request)
    {
        $filters = $request->only(['search', 'date_from', 'date_to']);

        return Excel::download(new CustomersExport($filters), 'customers-report-' . date('Y-m-d') . '.xlsx');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
        ]);

        $invoices = Invoice::whereIn('id', $request->invoice_ids)->get();

        foreach ($invoices as $invoice) {
            $data = ['status' => $request->status];

            if ($request->status === 'sent' && $invoice->status !== 'sent') {
                $data['sent_at'] = now();
            }

            if ($request->status === 'paid' && $invoice->status !== 'paid') {
                $data['paid_at'] = now();
            }

            $invoice->update($data);
        }

        return back()->with('success', count($invoices) . ' invoices updated successfully!');
    }
}
