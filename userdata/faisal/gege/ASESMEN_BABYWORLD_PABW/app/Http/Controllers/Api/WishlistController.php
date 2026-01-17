<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $wishlists = Wishlist::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'wishlist' => $wishlists->map(function ($w) {
                return [
                    'id'       => $w->product->id_produk,
                    'name'     => $w->product->nama_produk,
                    'category' => $w->product->kategori,
                    'price'    => $w->product->harga,
                    'image'    => $w->product->foto,
                ];
            })
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id_produk'
        ]);

        Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id
        ]);

        return response()->json(['message' => 'Added to wishlist']);
    }


    public function destroy(Request $request, $productId)
    {
        Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['message' => 'Removed from wishlist']);
    }
}
