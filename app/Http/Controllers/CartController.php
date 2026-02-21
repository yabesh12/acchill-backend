<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'User Carts';
        $auth_user = authSession();

        // Get all carts grouped by user
        $carts = Cart::with(['user', 'service', 'service.media'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $groupedCarts = $carts->groupBy('user_id')->map(function ($items, $userId) {
            $user = $items->first()->user;
            $cartTotal = 0;
            $itemsData = $items->map(function ($cart) use (&$cartTotal) {
                $service = $cart->service;
                $price = $service ? $service->price : 0;
                $discount = $service ? $service->discount : 0;
                $effectivePrice = $discount > 0 ? $price - ($price * $discount / 100) : $price;
                $lineTotal = $effectivePrice * $cart->quantity;
                $cartTotal += $lineTotal;
                return (object)[
                    'id' => $cart->id,
                    'service' => $service,
                    'quantity' => $cart->quantity,
                    'price' => $price,
                    'discount' => $discount,
                    'effective_price' => $effectivePrice,
                    'line_total' => $lineTotal,
                    'updated_at' => $cart->updated_at,
                ];
            });
            return (object)[
                'user' => $user,
                'items' => $itemsData,
                'total' => $cartTotal,
                'item_count' => $items->count(),
            ];
        })->sortByDesc('total');

        return view('cart.index', compact('pageTitle', 'auth_user', 'groupedCarts'));
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);
        $qty = max(1, intval($request->input('quantity', 1)));
        $cart->quantity = $qty;
        $cart->save();

        return response()->json([
            'message' => 'Quantity updated',
            'status' => true,
            'quantity' => $qty,
        ]);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        $userId = $cart->user_id;
        $cart->delete();

        $remaining = Cart::where('user_id', $userId)->count();

        return response()->json([
            'message' => 'Cart item deleted',
            'status' => true,
            'remaining' => $remaining,
        ]);
    }
}
