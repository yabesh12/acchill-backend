<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Service;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get current user's cart with service details
     */
    public function index(Request $request)
    {
        $cartItems = Cart::where('user_id', auth()->id())
            ->with(['service' => function ($query) {
                $query->select('id', 'name', 'price', 'discount', 'description', 'provider_id', 'status');
            }, 'service.media', 'service.providers'])
            ->get();

        $items = $cartItems->map(function ($cart) {
            $service = $cart->service;
            if (!$service) return null;

            $attachments = $service->getMedia('service_attachment')->map(function ($media) {
                return $media->getFullUrl();
            })->toArray();

            return [
                'id' => $cart->id,
                'service_id' => $cart->service_id,
                'service_name' => $service->name ?? '',
                'unit_price' => $service->price ?? 0,
                'discount' => $service->discount ?? 0,
                'quantity' => $cart->quantity,
                'image_url' => count($attachments) > 0 ? $attachments[0] : null,
                'total_rating' => $service->total_rating ?? 0,
                'total_review' => $service->total_review ?? 0,
                'description' => $service->description ?? '',
                'provider_id' => $service->provider_id,
            ];
        })->filter()->values();

        return response()->json([
            'data' => $items,
            'total_amount' => $items->sum(function ($item) {
                return $item['unit_price'] * $item['quantity'];
            }),
            'total_items' => $items->sum('quantity'),
        ]);
    }

    /**
     * Add a service to cart or increment quantity
     */
    public function add(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity' => 'integer|min:1',
        ]);

        $cart = Cart::where('user_id', auth()->id())
            ->where('service_id', $request->service_id)
            ->first();

        if ($cart) {
            $cart->quantity += $request->input('quantity', 1);
            $cart->save();
        } else {
            $cart = Cart::create([
                'user_id' => auth()->id(),
                'service_id' => $request->service_id,
                'quantity' => $request->input('quantity', 1),
            ]);
        }

        return response()->json([
            'message' => 'Added to cart',
            'cart_item' => $cart,
        ]);
    }

    /**
     * Update quantity of a cart item
     */
    public function update(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($request->quantity <= 0) {
            Cart::where('user_id', auth()->id())
                ->where('service_id', $request->service_id)
                ->delete();

            return response()->json(['message' => 'Removed from cart']);
        }

        $cart = Cart::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'service_id' => $request->service_id,
            ],
            ['quantity' => $request->quantity]
        );

        return response()->json([
            'message' => 'Cart updated',
            'cart_item' => $cart,
        ]);
    }

    /**
     * Remove a service from cart
     */
    public function remove(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        Cart::where('user_id', auth()->id())
            ->where('service_id', $request->service_id)
            ->delete();

        return response()->json(['message' => 'Removed from cart']);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Cart cleared']);
    }
}
