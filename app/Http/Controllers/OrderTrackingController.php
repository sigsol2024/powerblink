<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function index(): View
    {
        return view('pages.orders.track', [
            'title' => __('Track your order'),
            'order' => null,
        ]);
    }

    public function track(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'tracking_number' => ['required', 'string', 'max:32'],
        ]);

        $trackingNumber = strtoupper(trim($data['tracking_number']));

        $order = Order::query()
            ->with(['items', 'payment'])
            ->where(function ($q) use ($trackingNumber) {
                $q->where('order_number', $trackingNumber)
                    ->orWhere('tracking_number', $trackingNumber);
            })
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->withErrors(['tracking_number' => __('Tracking number not found.')]);
        }

        if (empty($order->tracking_number)) {
            $order->tracking_number = Order::generateUniqueTrackingNumber();
            $order->save();
        }

        return view('pages.orders.track', [
            'title' => __('Track your order'),
            'order' => $order,
        ]);
    }
}

