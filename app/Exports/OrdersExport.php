<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Order::with(['user', 'items']);

        // Apply filters
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Status',
            'Payment Status',
            'Subtotal',
            'Tax',
            'Shipping',
            'Total',
            'Payment Method',
            'Transaction ID',
            'Shipping Address',
            'Order Date',
            'Processed Date',
            'Shipped Date',
            'Delivered Date',
            'Notes',
            'Admin Notes',
        ];
    }

    public function map($order): array
    {
        $shippingAddress = collect([
            $order->shipping_address_line_1,
            $order->shipping_address_line_2,
            $order->shipping_city,
            $order->shipping_state,
            $order->shipping_postal_code,
            $order->shipping_country,
        ])->filter()->implode(', ');

        return [
            $order->order_number,
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone,
            ucfirst($order->status),
            ucfirst($order->payment_status),
            number_format($order->subtotal, 2),
            number_format($order->tax, 2),
            number_format($order->shipping, 2),
            number_format($order->total, 2),
            $order->payment_method,
            $order->transaction_id,
            $shippingAddress,
            $order->created_at->format('Y-m-d H:i:s'),
            $order->processed_at ? $order->processed_at->format('Y-m-d H:i:s') : '',
            $order->shipped_at ? $order->shipped_at->format('Y-m-d H:i:s') : '',
            $order->delivered_at ? $order->delivered_at->format('Y-m-d H:i:s') : '',
            $order->notes,
            $order->admin_notes,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Order Number
            'B' => 20, // Customer Name
            'C' => 25, // Customer Email
            'D' => 15, // Customer Phone
            'E' => 12, // Status
            'F' => 15, // Payment Status
            'G' => 12, // Subtotal
            'H' => 10, // Tax
            'I' => 12, // Shipping
            'J' => 12, // Total
            'K' => 15, // Payment Method
            'L' => 20, // Transaction ID
            'M' => 40, // Shipping Address
            'N' => 18, // Order Date
            'O' => 18, // Processed Date
            'P' => 18, // Shipped Date
            'Q' => 18, // Delivered Date
            'R' => 30, // Notes
            'S' => 30, // Admin Notes
        ];
    }

    public function title(): string
    {
        return 'Orders Report';
    }
}
