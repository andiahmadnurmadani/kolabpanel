<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Ambil cart
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang masih kosong!');
        }

        // Ambil data produk dari database
        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);

            if (!$product) continue;

            $itemSubtotal = $product->harga * $qty;
            $subtotal += $itemSubtotal;

            $items[] = [
                'id_produk'   => $product->id_produk,
                'nama_produk' => $product->nama_produk,
                'foto'        => $product->foto,
                'harga'       => $product->harga,
                'qty'         => $qty,
                'subtotal'    => $itemSubtotal
            ];
        }

        // ================
        // DISKON BERDASARKAN SESSION 'voucher' 
        // ================
        $voucher = session('voucher');

        $discountPercent = $voucher['diskon'] ?? 0;
        $voucherCode     = $voucher['kode'] ?? '';
        $discountValue   = intval($subtotal * ($discountPercent / 100));

        $finalTotal = max(0, $subtotal - $discountValue);

        return view('checkout', [
            'items'          => $items,
            'subtotal'       => $subtotal,
            'discount'       => $discountPercent,
            'voucher_code'   => $voucherCode,
            'discount_value' => $discountValue,
            'total'          => $finalTotal,
        ]);
    }

    public function placeOrder(Request $request)
    {
        // validasi
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'country' => 'required',
            'post_code' => 'required|numeric',
            'phone' => 'required',
            'email' => 'required|email',
            'payment_method' => 'required',
        ]);

        // Ambil data cart dari session
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kosong!');
        }

        // Hitung subtotal dari database
        $subtotal = 0;
        $detailItems = [];

        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);

            if (!$product) continue;

            $itemSubtotal = $product->harga * $qty;
            $subtotal += $itemSubtotal;

            $detailItems[] = [
                'id_produk' => $product->id_produk,
                'quantity'  => $qty,
                'subtotal'  => $itemSubtotal
            ];
        }

        // Hitung diskon
        $voucher = session('voucher');
        $discountPercent = $voucher['diskon'] ?? 0;
        $idVoucher = $voucher['id'] ?? null;
        $discountValue = intval($subtotal * ($discountPercent / 100));
        $finalTotal = max(0, $subtotal - $discountValue);

        DB::beginTransaction();

        try {
            // Simpan ke tabel tb_transaksi
            $transaksiId = DB::table('tb_transaksi')->insertGetId([
                'kode_transaksi'    => 'TRX-' . strtoupper(uniqid()),
                'id_user'           => Auth::id() ?? 0,
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
                'order_note'        => $request->order_note ?? null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Simpan detail transaksi
            foreach ($detailItems as $item) {
                DB::table('tb_detail_transaksi')->insert([
                    'id_transaksi' => $transaksiId,
                    'id_produk'    => $item['id_produk'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $item['subtotal'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            // Kurangi quantity voucher jika ada
            if ($idVoucher) {
                DB::table('tb_voucher')
                    ->where('id', $idVoucher)
                    ->decrement('quantity');
            }

            DB::commit();

            // Hapus cart dan voucher setelah order sukses
            session()->forget('cart');
            session()->forget('voucher');

            return redirect()->route('shop')
                ->with('success', 'Order berhasil dibuat! Kode transaksi: TRX-' . $transaksiId);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal membuat order: ' . $e->getMessage());
        }
    }
}