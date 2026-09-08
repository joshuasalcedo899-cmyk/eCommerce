<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalProducts = Product::count();

        $activeProducts = Product::where('is_active', true)
            ->count();
        
        $totalCategories = Category::count();

        $totalCustomers = User::where('role', 'customer')
            ->count();

        $totalOrders = Order::count();

        $pendingOrders = Order::where('status', 'pending')
            ->count();

        $processingOrders = Order::where('status', 'processing')
            ->count();

        $shippedOrders = Order::where('status', 'shipped')
            ->count();

        $deliveredOrders = Order::where('status', 'delivered')
            ->count();

        $cancelledOrders = Order::where('status', 'cancelled')
            ->count();

        $salesOrderQuery = fn () => Order::whereNotIn('status', ['cancelled']);

        $salesToday = $salesOrderQuery()
            ->whereDate('created_at', today())
            ->sum('total');

        $salesThisMonth = $salesOrderQuery()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total');

        $salesOrderCount = $salesOrderQuery()->count();
        $totalSales = $salesOrderQuery()->sum('total');
        $averageOrderValue = $salesOrderCount > 0
            ? $totalSales / $salesOrderCount
            : 0;

        $totalItemsSold = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNotIn('orders.status', ['cancelled'])
            ->sum('order_items.quantity');

        $salesTrend = collect(range(6, 0))->map(function (int $daysAgo) use ($salesOrderQuery) {
            $date = today()->subDays($daysAgo);
            $summary = $salesOrderQuery()
                ->whereDate('created_at', $date)
                ->selectRaw('COUNT(*) as orders, COALESCE(SUM(total), 0) as revenue')
                ->first();

            return [
                'date' => $date->format('M j'),
                'orders' => (int) $summary->orders,
                'revenue' => (float) $summary->revenue,
            ];
        });

        $maxTrendRevenue = max(1, $salesTrend->max('revenue'));

        $topSellingProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNotIn('orders.status', ['cancelled'])
            ->select('order_items.product_name')
            ->selectRaw('SUM(order_items.quantity) as units_sold')
            ->selectRaw('SUM(order_items.subtotal) as revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        $lowStockProducts = Product::with('category')
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get();

        $outOfStockProducts = Product::with('category')
            ->where('stock', 0)
            ->orderBy('name')
            ->get();

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'totalCategories',
            'totalCustomers',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'deliveredOrders',
            'cancelledOrders',
            'salesToday',
            'salesThisMonth',
            'averageOrderValue',
            'totalItemsSold',
            'salesTrend',
            'maxTrendRevenue',
            'topSellingProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'recentOrders',
        ));
    }
}
