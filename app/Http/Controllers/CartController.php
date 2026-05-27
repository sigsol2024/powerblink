<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Support\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            return $this->jsonState();
        }

        return view('pages.cart.index', [
            'title' => __('Shopping Bag'),
            'lines' => Cart::lines(),
            'subtotal' => Cart::subtotal(),
        ]);
    }

    public function add(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'integer', 'min:1'],
            'vehicle_variant_id' => ['nullable', 'integer', 'min:1'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $variantId = isset($data['vehicle_variant_id']) ? (int) $data['vehicle_variant_id'] : null;

        $vehicleId = (int) $data['vehicle_id'];
        Cart::add($vehicleId, (int) ($data['qty'] ?? 1), $variantId);

        if ($request->wantsJson()) {
            $productName = (string) (Vehicle::query()->whereKey($vehicleId)->value('title') ?? '');

            return $this->jsonState(__('Added to your bag.'), $productName);
        }

        return back()->with('status', __('Added to your bag.'));
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'integer', 'min:1'],
            'vehicle_variant_id' => ['nullable', 'integer', 'min:1'],
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $variantId = isset($data['vehicle_variant_id']) ? (int) $data['vehicle_variant_id'] : null;

        Cart::updateQty((int) $data['vehicle_id'], (int) $data['qty'], $variantId);

        if ($request->wantsJson()) {
            return $this->jsonState(__('Bag updated.'));
        }

        return redirect()->route('cart.index')->with('status', __('Bag updated.'));
    }

    public function remove(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'integer', 'min:1'],
            'vehicle_variant_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $variantId = isset($data['vehicle_variant_id']) ? (int) $data['vehicle_variant_id'] : null;

        Cart::remove((int) $data['vehicle_id'], $variantId);

        if ($request->wantsJson()) {
            return $this->jsonState(__('Item removed.'));
        }

        return redirect()->route('cart.index')->with('status', __('Item removed.'));
    }

    private function jsonState(?string $message = null, ?string $addedProductName = null): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'message' => $message,
            'added_product_name' => $addedProductName,
            'count' => Cart::count(),
            'item_count' => Cart::itemCount(),
            'subtotal' => Cart::subtotal(),
            'subtotal_formatted' => format_currency(Cart::subtotal()),
            'lines' => array_map(function (array $line) {
                return [
                    'vehicle_id' => (int) $line['vehicle_id'],
                    'vehicle_variant_id' => isset($line['vehicle_variant_id']) ? (int) $line['vehicle_variant_id'] : null,
                    'name' => (string) $line['name'],
                    'image' => $line['image'] ?? null,
                    'sku' => $line['sku'] ?? null,
                    'variant_label' => $line['variant_label'] ?? null,
                    'qty' => (int) $line['qty'],
                    'unit_price' => (int) $line['unit_price'],
                    'unit_price_formatted' => format_currency((int) $line['unit_price']),
                    'line_total_formatted' => format_currency((int) $line['unit_price'] * (int) $line['qty']),
                ];
            }, Cart::lines()),
        ]);
    }
}
