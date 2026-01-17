<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ShopController extends Controller
{
    // READ - List & Search
    public function index(Request $request)
    {
        $search = strtolower($request->get('search', ''));
        $kategori = $request->get('kategori', '');

        //Ambil data dari database (tb_produk)
        $products = Product::when($kategori, function($query) use ($kategori) {
                return $query->where('kategori', $kategori);
            })
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->whereRaw('LOWER(nama_produk) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('LOWER(kategori) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('LOWER(deskripsi) LIKE ?', ["%{$search}%"]);
                });
            })
            ->get();

        return view('shop', [
            'products' => $products,
            'kategori' => $kategori,
            'search' => $search
        ]);	
    }
}