<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KeranjangResource;
use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KeranjangController extends Controller
{
    public function getKeranjangByPembeliId()
    {
        $user = Auth::guard('api')->user();

        // Mendapatkan isi keranjang berdasarkan pembeli_id
        $keranjang = Keranjang::with('produk')->where('pembeli_id', $user->id)->get();

        return new KeranjangResource(true, 'List Pesanan di keranjang', $keranjang);
    }

    public function masukKeranjang(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produk_id'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create produk
        $produk = Keranjang::create([
            'pembeli_id' => Auth::guard('api')->user()->id,
            'produk_id' => $request->produk_id,
        ]);

        if ($produk) {
            //return success with Api Resource
            return new KeranjangResource(true, 'Berhasil dimasukkan ke Keranjang!', $produk);
        }

        //return failed with Api Resource
        return new KeranjangResource(false, 'Gagal dimasukkan ke Keranjang!', null);
    }

    public function batalkanMasukkanKeranjang($id)
    {
        // Cari keranjang berdasarkan ID
        $keranjang = Keranjang::find($id);

        // Pastikan keranjang ditemukan
        if (!$keranjang) {
            return new KeranjangResource(false, 'Keranjang tidak ditemukan!', null);
        }

        // Pastikan bahwa pengguna yang mengakses memiliki hak akses ke keranjang
        $user = Auth::guard('api')->user();
        if ($user->id !== $keranjang->pembeli_id) {
            return new KeranjangResource(false, 'Anda tidak memiliki hak akses untuk membatalkan masukkan ini!', null);
        }

        // Hapus keranjang
        $result = $keranjang->delete();

        if ($result) {
            return new KeranjangResource(true, 'Berhasil membatalkan masukkan ke Keranjang!', null);
        }

        return new KeranjangResource(false, 'Gagal membatalkan masukkan ke Keranjang!', null);
    }
}
