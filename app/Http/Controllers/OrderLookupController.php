<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class OrderLookupController extends Controller
{
    public function index(): View
    {
        return view('pages.orders.lookup', [
            'title' => __('Find your orders'),
        ]);
    }

    public function lookup(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'customer_email' => ['required', 'email', 'max:190'],
            'order_number' => ['nullable', 'string', 'max:32'],
        ]);

        $email = strtolower(trim($data['customer_email']));
        $orderNumber = isset($data['order_number']) ? trim($data['order_number']) : '';

        $query = Order::query()
            ->whereRaw('LOWER(customer_email) = ?', [$email])
            ->where('status', 'paid')
            ->latest();

        if ($orderNumber !== '') {
            $query->where('order_number', $orderNumber);
        }

        $orders = $query->limit(50)->get();

        if ($orders->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['customer_email' => __('No paid orders found for this email.')]);
        }

        $orderLinks = $orders->mapWithKeys(fn (Order $order) => [
            $order->id => URL::temporarySignedRoute(
                'order.show',
                now()->addDays(90),
                ['order' => $order->id]
            ),
        ]);

        return view('pages.orders.history', [
            'title' => __('Your orders'),
            'orders' => $orders,
            'orderLinks' => $orderLinks,
            'customerEmail' => $data['customer_email'],
        ]);
    }
}
