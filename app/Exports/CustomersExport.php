<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::with(['orders'])->where('role', 'customer');

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Customer ID',
            'Name',
            'Email',
            'Phone',
            'Address',
            'City',
            'State',
            'Postal Code',
            'Country',
            'Total Orders',
            'Total Spent',
            'Last Order Date',
            'Registration Date',
            'Email Verified',
            'Status',
        ];
    }

    public function map($customer): array
    {
        $totalSpent = $customer->orders->sum('total');
        $lastOrder = $customer->orders->sortByDesc('created_at')->first();

        return [
            $customer->id,
            $customer->name,
            $customer->email,
            $customer->phone,
            $customer->address,
            $customer->city,
            $customer->state,
            $customer->postal_code,
            $customer->country,
            $customer->orders->count(),
            number_format($totalSpent, 2),
            $lastOrder ? $lastOrder->created_at->format('Y-m-d H:i:s') : '',
            $customer->created_at->format('Y-m-d H:i:s'),
            $customer->email_verified_at ? 'Yes' : 'No',
            $customer->email_verified_at ? 'Active' : 'Pending',
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
            'A' => 12, // Customer ID
            'B' => 20, // Name
            'C' => 25, // Email
            'D' => 15, // Phone
            'E' => 30, // Address
            'F' => 15, // City
            'G' => 15, // State
            'H' => 12, // Postal Code
            'I' => 15, // Country
            'J' => 12, // Total Orders
            'K' => 15, // Total Spent
            'L' => 18, // Last Order Date
            'M' => 18, // Registration Date
            'N' => 15, // Email Verified
            'O' => 12, // Status
        ];
    }

    public function title(): string
    {
        return 'Customers Report';
    }
}
