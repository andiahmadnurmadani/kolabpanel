<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/admin/products
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Product::all()
        ]);
    }

    // POST /api/admin/products
    public function store(Request $request)
    {
        $product = Product::create([
            'nama_produk' => $request->nama_produk,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'harga'       => $request->harga,
            'foto'        => $request->foto,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $product
        ], 201);
    }

    // PUT /api/admin/products/{id_produk}
    public function update(Request $request, $id_produk)
    {
        $product = Product::where('id_produk', $id_produk)->firstOrFail();

        $product->update([
            'nama_produk' => $request->nama_produk,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'harga'       => $request->harga,
            'foto'        => $request->foto,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil diupdate',
            'data' => $product
        ]);
    }

    // DELETE /api/admin/products/{id_produk}
    public function destroy($id_produk)
    {
        Product::where('id_produk', $id_produk)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }

    // GET /api/admin/products/search?q=xxx
    public function search(Request $request)
    {
        $q = $request->query('q');

        $products = Product::where('nama_produk', 'like', "%$q%")
            ->orWhere('deskripsi', 'like', "%$q%")
            ->get();

        return response()->json([
            'status' => true,
            'data' => $products
        ]);
    }
}