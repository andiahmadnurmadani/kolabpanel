<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;

class CheckoutController extends Controller
{
    /**
     * PREVIEW CHECKOUT (optional tapi rapi)
     * Mirip index() di web, tapi return JSON
     */
    public function preview(Request $request)
    {
        $user = $request->user(); // dari Sanctum

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang masih kosong'
            ], 400);
        }

        $products = Product::all()->keyBy('id_produk');

        $items = [];
        $subtotal = 0;

        foreach ($cart as $id => $qty) {
            if (!isset($products[$id])) continue;

            $p = $products[$id];
            $itemSubtotal = $p->harga * $qty;
            $subtotal += $itemSubtotal;

            $items[] = [
                'id_produk'   => $p->id_produk,
                'nama_produk' => $p->nama_produk,
                'harga'       => $p->harga,
                'qty'         => $qty,
                'subtotal'    => $itemSubtotal
            ];
        }

        $voucher     = session('voucher');
        $discountPct = $voucher['diskon'] ?? 0;
        $discountVal = intval($subtotal * ($discountPct / 100));
        $total       = max(0, $subtotal - $discountVal);

        return response()->json([
            'success' => true,
            'data' => [
                'items'            => $items,
                'subtotal'         => $subtotal,
                'discount_percent' => $discountPct,
                'discount_value'   => $discountVal,
                'total'            => $total
            ]
        ]);
    }

    /**
     * PLACE ORDER
     * Adaptasi langsung dari placeOrder() web
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'name'           => 'required',
            'address'        => 'required',
            'city'           => 'required',
            'country'        => 'required',
            'post_code'      => 'required',
            'phone'          => 'required',
            'email'          => 'required|email',
            'payment_method' => 'required'
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong'
            ], 400);
        }

        $products = Product::all()->keyBy('id_produk');

        $subtotal = 0;
        $detailItems = [];

        foreach ($cart as $id => $qty) {
            if (!isset($products[$id])) continue;

            $p = $products[$id];
            $sub = $p->harga * $qty;
            $subtotal += $sub;

            $detailItems[] = [
                'id_produk' => $p->id_produk,
                'quantity'  => $qty,
                'subtotal'  => $sub
            ];
        }

        // Voucher
        $voucher     = session('voucher');
        $discountPct = $voucher['diskon'] ?? 0;
        $idVoucher   = $voucher['id'] ?? null;

        $discountVal = intval($subtotal * ($discountPct / 100));
        $finalTotal  = max(0, $subtotal - $discountVal);

        $kodeTransaksi = 'TRX-' . strtoupper(Str::random(8));

        DB::beginTransaction();

        try {
            $idTransaksi = DB::table('tb_transaksi')->insertGetId([
                'kode_transaksi'    => $kodeTransaksi,
                'id_user'           => $user->id,
                'id_admin'          => null,
                'id_voucher'        => $idVoucher,
                'total_harga'       => $finalTotal,
                'metode_pembayaran' => $request->payment_method,
                'status_order'      => 'menunggu',
                'status_pembayaran' => 'belum bayar',
                'address'           => $request->address,
                'city'              => $request->city,
                'country'           => $request->country,
                'post_code'         => $request->post_code,
                'order_note'        => $request->order_note,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            foreach ($detailItems as $item) {
                DB::table('tb_detail_transaksi')->insert([
                    'id_transaksi' => $idTransaksi,
                    'id_produk'    => $item['id_produk'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $item['subtotal'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            if ($idVoucher) {
                DB::table('tb_voucher')
                    ->where('id', $idVoucher)
                    ->decrement('quantity');
            }

            DB::commit();

            session()->forget('cart');
            session()->forget('voucher');

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat',
                'data' => [
                    'kode_transaksi' => $kodeTransaksi,
                    'total'          => $finalTotal
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat order',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}