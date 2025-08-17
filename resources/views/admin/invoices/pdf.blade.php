<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .company-tagline {
            font-size: 14px;
            color: #666;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info, .customer-info {
            flex: 1;
        }
        .invoice-info {
            text-align: right;
        }
        .customer-info {
            text-align: left;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2563eb;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f8fafc;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            font-weight: bold;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .totals {
            width: 100%;
            margin-left: auto;
            margin-bottom: 30px;
        }
        .totals table {
            width: 300px;
            margin-left: auto;
        }
        .totals td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .totals .total-row {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #2563eb;
            border-bottom: 2px solid #2563eb;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-draft { background-color: #f3f4f6; color: #6b7280; }
        .status-sent { background-color: #dbeafe; color: #1d4ed8; }
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-overdue { background-color: #fee2e2; color: #dc2626; }
        .status-cancelled { background-color: #f3f4f6; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">SportApp</div>
        <div class="company-tagline">Your Ultimate Sports Equipment Store</div>
    </div>

    <div class="invoice-details">
        <div class="customer-info">
            <div class="section-title">Bill To:</div>
            <div class="info-row">
                <span class="info-label">Name:</span> {{ $invoice->order->customer_name }}
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span> {{ $invoice->order->customer_email }}
            </div>
            @if($invoice->order->customer_phone)
            <div class="info-row">
                <span class="info-label">Phone:</span> {{ $invoice->order->customer_phone }}
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Address:</span><br>
                {{ $invoice->order->shipping_address_line_1 }}<br>
                @if($invoice->order->shipping_address_line_2)
                    {{ $invoice->order->shipping_address_line_2 }}<br>
                @endif
                {{ $invoice->order->shipping_city }}, {{ $invoice->order->shipping_state }} {{ $invoice->order->shipping_postal_code }}<br>
                {{ $invoice->order->shipping_country }}
            </div>
        </div>

        <div class="invoice-info">
            <div class="section-title">Invoice Details:</div>
            <div class="info-row">
                <span class="info-label">Invoice #:</span> {{ $invoice->invoice_number }}
            </div>
            <div class="info-row">
                <span class="info-label">Order #:</span> {{ $invoice->order->order_number }}
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span> {{ $invoice->invoice_date->format('M d, Y') }}
            </div>
            @if($invoice->due_date)
            <div class="info-row">
                <span class="info-label">Due Date:</span> {{ $invoice->due_date->format('M d, Y') }}
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>SKU</th>
                <th>Size</th>
                <th>Color</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->product_sku }}</td>
                <td>{{ $item->size ?? '-' }}</td>
                <td>{{ $item->color ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
                <td>
                    @if($item->discount_amount > 0)
                        ${{ number_format($item->discount_amount, 2) }}
                    @else
                        -
                    @endif
                </td>
                <td>${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td>${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->tax > 0)
            <tr>
                <td>Tax:</td>
                <td>${{ number_format($invoice->tax, 2) }}</td>
            </tr>
            @endif
            @if($invoice->shipping > 0)
            <tr>
                <td>Shipping:</td>
                <td>${{ number_format($invoice->shipping, 2) }}</td>
            </tr>
            @endif
            @if($invoice->discount > 0)
            <tr>
                <td>Discount:</td>
                <td>-${{ number_format($invoice->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total:</td>
                <td>${{ number_format($invoice->total, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes)
    <div style="margin-bottom: 30px;">
        <div class="section-title">Notes:</div>
        <div>{{ $invoice->notes }}</div>
    </div>
    @endif

    @if($invoice->terms_conditions)
    <div style="margin-bottom: 30px;">
        <div class="section-title">Terms & Conditions:</div>
        <div>{{ $invoice->terms_conditions }}</div>
    </div>
    @endif

    <div class="footer">
        <p><strong>Thank you for your business!</strong></p>
        <p>For any questions regarding this invoice, please contact us at support@sportapp.com</p>
        <p>Payment is due within 30 days of invoice date unless otherwise specified.</p>
    </div>
</body>
</html>
