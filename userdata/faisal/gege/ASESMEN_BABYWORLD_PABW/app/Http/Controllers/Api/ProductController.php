<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Untuk halaman Home Flutter
     */
    public function index()
    {
        $products = Product::all()->map(function ($product) {
            return [
                'id' => $product->id_produk,
                'name' => $product->nama_produk,
                'category' => $product->kategori,
                'price' => $product->harga,
                'image' => $product->foto
                    ? $product->foto . '?auto=compress&cs=tinysrgb&w=600'
                    : 'https://images.pexels.com/photos/459957/pexels-photo-459957.jpeg?auto=compress&cs=tinysrgb&w=600',

            ];
        });

        return response()->json([
            'products' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::where('id_produk', $id)->firstOrFail();

        return response()->json([
            'id' => $product->id_produk,
            'name' => $product->nama_produk,
            'category' => $product->kategori,
            'price' => $product->harga,
            'description' => $product->deskripsi,
            'image' => $product->foto,
        ]);
    }
}
