<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Http\Resources\TransaksiResource;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function getAll()
    {
        $transaksi = Transaksi::with('produk', 'pembeli')->get();
        return new TransaksiResource(true, 'List Semua Transaksi', $transaksi);
    }

    public function detailTransaksi($order_id)
    {
        $transaksi = Transaksi::with('produk', 'pembeli')
            ->where('order_id', $order_id)
            ->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        }

        return new TransaksiResource(true, 'Detail Pesanan', $transaksi);
    }

    public function getTransaksiByPembeliId()
    {
        $user = Auth::user(); // Mengambil pengguna yang sedang terotentikasi

        if ($user) {
            $transaksi = Transaksi::with('produk')
                ->where('pembeli_id', $user->id)
                ->get();

            return new TransaksiResource(true, 'List Transaksi Berdasarkan Pembeli ID', $transaksi);
        } else {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk operasi ini.'], 403);
        }
    }

    public function showPesananByOrderId($order_id)
    {
        $transaksi = Transaksi::with('produk')
            ->where('order_id', $order_id)
            ->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        }

        return new TransaksiResource(true, 'Detail Pesanan', $transaksi);
    }


    public function pesanProduk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produk_id'     => 'required',
            'total_harga'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create produk
        $produk = Transaksi::create([
            'order_id' => Str::uuid(),
            'produk_id' => $request->produk_id,
            'pembeli_id' => Auth::guard('api')->user()->id,
            'total_harga' => $request->total_harga,
            'status' => 'pending',
        ]);

        if ($produk) {
            //return success with Api Resource
            return new TransaksiResource(true, 'Berhasil Melakukan Pesanan!', $produk);
        }

        //return failed with Api Resource
        return new TransaksiResource(false, 'Gagal Melakukan Pesanan!', null);
    }

    // Metode untuk membatalkan pesanan
    public function batalkanPesanan($id)
    {
        $user = Auth::guard('api')->user();

        // Mencari transaksi berdasarkan ID
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return new TransaksiResource(false, 'Transaksi tidak ditemukan!', null);
        }

        // Memeriksa apakah pengguna yang mengakses memiliki hak akses untuk membatalkan pesanan
        if ($user->id !== $transaksi->pembeli_id) {
            return new TransaksiResource(false, 'Anda tidak memiliki hak akses untuk membatalkan pesanan ini!', null);
        }

        // Memeriksa status pesanan dan produk sebelum membatalkan
        if ($transaksi->status === 'pending') {
            // Melakukan pembatalan pesanan
            $transaksi->delete();

            return new TransaksiResource(true, 'Pesanan berhasil dibatalkan!', null);
        }

        return new TransaksiResource(false, 'Tidak dapat membatalkan pesanan yang sudah diproses atau produk tidak lagi dalam status pending!', null);
    }
}
