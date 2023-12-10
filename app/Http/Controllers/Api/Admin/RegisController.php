<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|confirmed',
            'role'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create user
        $user = User::create([
            'nama'      => $request->nama,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'role'      => $request->role,
        ]);

        if ($user) {
            return new UserResource(true, 'Berhasil Mendaftar, Silahkan Login!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Gagal Mendaftar!', null);
    }
}
