<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
            'lowStockProducts',
            'outOfStockProducts',
            'recentOrders',
        ));
    }
}