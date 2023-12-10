<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//route login
Route::post('/login', App\Http\Controllers\Api\LoginController::class);

//route regis
Route::post('/regis', [App\Http\Controllers\Api\RegisController::class, 'index']);

// Group route with middleware "auth:api"
Route::group(['middleware' => 'auth:api'], function () {

    // Route for user logged in
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    // Route for logout
    Route::post('/logout', 'App\Http\Controllers\Api\LogoutController@logout');

    // Routes for 'penjual' role
    Route::group(['middleware' => 'role:penjual'], function () {
        Route::apiResource('/produks', 'App\Http\Controllers\Api\ProdukController', ['except' => ['create', 'edit']]);

        // transaksi
        Route::get('/transaksi', [App\Http\Controllers\Api\TransaksiController::class, 'getAll']);
        Route::get('/transaksi/{order_id}', [App\Http\Controllers\Api\TransaksiController::class, 'detailTransaksi']);
    });

    // Routes for 'pembeli' role
    Route::group(['middleware' => 'role:pembeli'], function () {
        // keranjang
        Route::get('/keranjang', [App\Http\Controllers\Api\KeranjangController::class, 'getKeranjangByPembeliId']);
        Route::post('/masukkan-keranjang', [App\Http\Controllers\Api\KeranjangController::class, 'masukKeranjang']);
        Route::delete('/batalkan-masukkan-keranjang/{id}', [App\Http\Controllers\Api\KeranjangController::class, 'batalkanMasukkanKeranjang']);

        // pesan produk
        Route::get('/pesanan', [App\Http\Controllers\Api\TransaksiController::class, 'getTransaksiByPembeliId']);
        Route::get('/pesanan/{order_id}', [App\Http\Controllers\Api\TransaksiController::class, 'showPesananByOrderId']);
        Route::post('/pesan-produk', [App\Http\Controllers\Api\TransaksiController::class, 'pesanProduk']);
        Route::delete('/batalkan-pesanan/{id}', [App\Http\Controllers\Api\TransaksiController::class, 'batalkanPesanan']);
    });
});
