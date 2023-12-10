<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProdukResource;
use App\Models\Produk;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    public function index()
    {
        //get produk
        $produk = Produk::when(request()->q, function ($produk) {
            $produk = $produk->where('nama', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new ProdukResource(true, 'List Data Produk', $produk);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto'    => 'required|mimes:jpeg,jpg,png|max:5000',
            'nama'     => 'required|unique:produks',
            'harga'     => 'required',
            'stok'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload foto
        $foto = $request->file('foto');
        $foto->storeAs('public/produk', $foto->hashName());

        //create produk
        $produk = Produk::create([
            'foto' => $foto->hashName(),
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
        ]);

        if ($produk) {
            //return success with Api Resource
            return new ProdukResource(true, 'Data Produk Berhasil Disimpan!', $produk);
        }

        //return failed with Api Resource
        return new ProdukResource(false, 'Data Produk Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $produk = Produk::whereId($id)->first();

        if ($produk) {
            //return success with Api Resource
            return new ProdukResource(true, 'Detail Data Produk!', $produk);
        }

        //return failed with Api Resource
        return new ProdukResource(false, 'Detail Data Produk Tidak Ditemukan!', null);
    }

    public function update(Request $request, Produk $produk)
    {
        $validator = Validator::make($request->all(), [
            'nama'     => 'required|unique:produks,nama,' . $produk->id,
            'harga'     => 'required',
            'stok'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check foto update
        if ($request->file('foto')) {

            //remove old foto
            Storage::disk('local')->delete('public/produk/' . basename($produk->foto));

            //upload new foto
            $foto = $request->file('foto');
            $foto->storeAs('public/produk', $foto->hashName());

            //update produk with new foto
            $produk->update([
                'foto' => $foto->hashName(),
                'nama' => $request->nama,
                'harga'     => $request->harga,
                'stok'     => $request->stok,
            ]);
        }

        //update produk without foto
        $produk->update([
            'nama' => $request->nama,
            'harga'     => $request->harga,
            'stok'     => $request->stok,
        ]);

        if ($produk) {
            //return success with Api Resource
            return new ProdukResource(true, 'Data Produk Berhasil Diupdate!', $produk);
        }

        //return failed with Api Resource
        return new ProdukResource(false, 'Data Produk Gagal Diupdate!', null);
    }

    public function destroy(Produk $produk)
    {
        //remove foto
        Storage::disk('local')->delete('public/produk/' . basename($produk->foto));

        if ($produk->delete()) {
            //return success with Api Resource
            return new ProdukResource(true, 'Data Produk Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new ProdukResource(false, 'Data Produk Gagal Dihapus!', null);
    }
}
