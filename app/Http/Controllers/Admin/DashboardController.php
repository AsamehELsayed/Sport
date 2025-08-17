<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::pending()->count(),
            'processing_orders' => Order::processing()->count(),
            'shipped_orders' => Order::shipped()->count(),
            'delivered_orders' => Order::delivered()->count(),
            'cancelled_orders' => Order::cancelled()->count(),
            'total_customers' => User::count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
        ];

        // Get recent orders
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(10)
            ->get();

        // Get orders by status for chart
        $ordersByStatus = [
            'Pending' => Order::pending()->count(),
            'Processing' => Order::processing()->count(),
            'Shipped' => Order::shipped()->count(),
            'Delivered' => Order::delivered()->count(),
            'Cancelled' => Order::cancelled()->count(),
        ];

        // Get monthly revenue for the last 6 months
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Order::where('status', '!=', 'cancelled')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total');

            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
            ];
        }

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'ordersByStatus' => $ordersByStatus,
            'monthlyRevenue' => $monthlyRevenue,
        ]);
    }
}
