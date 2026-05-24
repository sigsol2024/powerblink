<?php

namespace App\Http\Controllers;

use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('pages.cart.index', [
            'title' => __('Shopping Bag'),
            'lines' => Cart::lines(),
            'subtotal' => Cart::subtotal(),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'integer', 'min:1'],
            'vehicle_variant_id' => ['nullable', 'integer', 'min:1'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $variantId = isset($data['vehicle_variant_id']) ? (int) $data['vehicle_variant_id'] : null;

        Cart::add((int) $data['vehicle_id'], (int) ($data['qty'] ?? 1), $variantId);

        return back()->with('status', __('Added to your bag.'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'integer', 'min:1'],
            'vehicle_variant_id' => ['nullable', 'integer', 'min:1'],
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $variantId = isset($data['vehicle_variant_id']) ? (int) $data['vehicle_variant_id'] : null;

        Cart::updateQty((int) $data['vehicle_id'], (int) $data['qty'], $variantId);

        return redirect()->route('cart.index')->with('status', __('Bag updated.'));
    }

    public function remove(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'integer', 'min:1'],
            'vehicle_variant_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $variantId = isset($data['vehicle_variant_id']) ? (int) $data['vehicle_variant_id'] : null;

        Cart::remove((int) $data['vehicle_id'], $variantId);

        return redirect()->route('cart.index')->with('status', __('Item removed.'));
    }
}
