<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = trim((string) $request->query('status', ''));

        $orders = Order::query()
            ->withCount('items')
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statusKeys = ['pending_payment', 'paid', 'fulfilled', 'failed', 'cancelled', 'refunded'];
        $statusCounts = ['all' => Order::query()->count()];
        foreach ($statusKeys as $key) {
            $statusCounts[$key] = Order::query()->where('status', $key)->count();
        }

        return view('admin.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'statusCounts' => $statusCounts,
            'pageStatuses' => $orders->pluck('status')->values()->all(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.vehicle', 'items.variant', 'payment']);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending_payment,paid,failed,cancelled,fulfilled,refunded'],
        ]);

        $order->update(['status' => $data['status']]);

        return back()->with('status', __('Order status updated.'));
    }
}
