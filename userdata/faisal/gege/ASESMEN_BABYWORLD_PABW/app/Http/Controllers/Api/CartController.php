<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Voucher;
use Carbon\Carbon;

class CartController extends Controller
{
    /**
     * Ambil produk
     */
    private function findProduct($id)
    {
        return Product::find($id);
    }

    /**
     * Ambil cart user (CACHE)
     */
    private function getCart($userId)
    {
        return cache()->get('cart_'.$userId, []);
    }

    /**
     * Simpan cart user (CACHE)
     */
    private function saveCart($userId, $cart)
    {
        cache()->put('cart_'.$userId, $cart, now()->addHours(2));
    }

    /**
     * GET CART
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $cart = $this->getCart($user->id);
        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $qty) {
            $product = $this->findProduct($productId);
            if (!$product) continue;

            $rowSubtotal = $product->harga * $qty;
            $subtotal += $rowSubtotal;

            $items[] = [
                'id_produk'   => $product->id_produk,
                'nama_produk' => $product->nama_produk,
                'harga'       => $product->harga,
                'quantity'    => $qty,
                'subtotal'    => $rowSubtotal,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items'    => $items,
                'subtotal' => $subtotal,
            ]
        ]);
    }

    /**
     * ADD TO CART
     */
    public function add(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'id_produk' => 'required|integer'
        ]);

        $product = $this->findProduct($request->id_produk);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $cart = $this->getCart($user->id);
        $cart[$product->id_produk] = ($cart[$product->id_produk] ?? 0) + 1;
        $this->saveCart($user->id, $cart);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke cart'
        ]);
    }

    /**
     * UPDATE CART
     */
    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'id_produk' => 'required|integer',
            'action'    => 'required|in:inc,dec'
        ]);

        $cart = $this->getCart($user->id);

        if (!isset($cart[$request->id_produk])) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ada di cart'
            ], 404);
        }

        $cart[$request->id_produk] =
            $request->action === 'inc'
                ? $cart[$request->id_produk] + 1
                : max(1, $cart[$request->id_produk] - 1);

        $this->saveCart($user->id, $cart);

        return response()->json([
            'success' => true,
            'message' => 'Cart berhasil diperbarui'
        ]);
    }

    /**
     * REMOVE CART ITEM
     */
    public function remove(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'id_produk' => 'required|integer'
        ]);

        $cart = $this->getCart($user->id);
        unset($cart[$request->id_produk]);
        $this->saveCart($user->id, $cart);

        return response()->json([
            'success' => true,
            'message' => 'Produk dihapus dari cart'
        ]);
    }

    /**
     * APPLY VOUCHER
     */
    public function applyVoucher(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'kode_voucher' => 'required|string'
        ]);

        // Cari voucher berdasarkan kode
        $voucher = Voucher::where('kode_voucher', $request->kode_voucher)
            ->where('status', 'active')
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak ditemukan atau tidak valid'
            ], 404);
        }

        // Cek tanggal berlaku
        $now = Carbon::now();
        if ($now->lt($voucher->tanggal_berlaku)) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher belum dapat digunakan'
            ], 400);
        }

        // Cek expired
        if ($now->gt($voucher->tanggal_expired)) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah kadaluarsa'
            ], 400);
        }

        // Cek quantity
        if ($voucher->quantity <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah habis'
            ], 400);
        }

        // Hitung subtotal dari cart
        $cart = $this->getCart($user->id);
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart masih kosong'
            ], 400);
        }

        $subtotal = 0;
        foreach ($cart as $productId => $qty) {
            $product = $this->findProduct($productId);
            if ($product) {
                $subtotal += $product->harga * $qty;
            }
        }

        // Hitung diskon berdasarkan jenis voucher
        $discount = 0;
        if ($voucher->jenis_voucher === 'percentage') {
            $discount = ($subtotal * $voucher->diskon) / 100;
        } else {
            // Fixed amount
            $discount = min($voucher->diskon, $subtotal);
        }

        $total = $subtotal - $discount;

        // Simpan voucher ke cache (opsional)
        cache()->put('voucher_'.$user->id, [
            'kode_voucher' => $voucher->kode_voucher,
            'discount' => $discount
        ], now()->addHours(2));

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan',
            'data' => [
                'voucher' => [
                    'kode' => $voucher->kode_voucher,
                    'jenis' => $voucher->jenis_voucher,
                    'diskon' => $voucher->diskon,
                    'deskripsi' => $voucher->deskripsi
                ],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total
            ]
        ], 200);
    }
}