<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'User Carts';
        $auth_user = authSession();
        return view('cart.index', compact('pageTitle', 'auth_user'));
    }

    public function index_data(Request $request)
    {
        $carts = Cart::with(['user', 'service'])
            ->select('carts.*')
            ->orderBy('carts.updated_at', 'desc');

        return DataTables::of($carts)
            ->addColumn('user_name', function ($cart) {
                $user = $cart->user;
                return $user ? ($user->first_name . ' ' . $user->last_name . '<br><small class="text-muted">' . $user->email . '</small>') : '-';
            })
            ->addColumn('service_name', function ($cart) {
                $service = $cart->service;
                if (!$service) return '-';
                $img = '';
                $media = $service->getFirstMedia('service_attachment');
                if ($media) {
                    $img = '<img src="' . $media->getUrl() . '" class="rounded me-2" style="width:40px;height:40px;object-fit:cover">';
                }
                return $img . '<strong>' . e($service->name) . '</strong>';
            })
            ->addColumn('service_price', function ($cart) {
                $service = $cart->service;
                return $service ? getPriceFormat($service->price) : '-';
            })
            ->addColumn('total', function ($cart) {
                $service = $cart->service;
                $price = $service ? $service->price : 0;
                $discount = $service ? $service->discount : 0;
                $effectivePrice = $discount > 0 ? $price - ($price * $discount / 100) : $price;
                return '<strong class="text-primary">' . getPriceFormat($effectivePrice * $cart->quantity) . '</strong>';
            })
            ->addColumn('action', function ($cart) {
                return '<button class="btn btn-sm btn-danger" onclick="deleteCart(' . $cart->id . ')"><i class="fas fa-trash"></i></button>';
            })
            ->rawColumns(['user_name', 'service_name', 'total', 'action'])
            ->make(true);
    }

    public function destroy($id)
    {
        Cart::findOrFail($id)->delete();
        return response()->json(['message' => 'Cart item deleted', 'status' => true]);
    }
}
