<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//route login
Route::post('/login', App\Http\Controllers\Api\Admin\LoginController::class);

//route regis
Route::post('/regis', [App\Http\Controllers\Api\Admin\RegisController::class, 'index']);

//group route with middleware "auth:api"
Route::group(['middleware' => 'auth:api'], function () {

    //route user logged in
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    //route logout
    Route::post('/logout', App\Http\Controllers\Api\Admin\LogoutController::class);
});
