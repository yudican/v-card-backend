<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        $validate = Validator::make($request->all(), [
            'username'  => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            $respon = [
                'error' => true,
                'status_code' => 401,
                'message' => 'Maaf, Silahkan isi semua form yang tersedia',
                'messages' => $validate->errors(),
            ];
            return response()->json($respon, 401);
        }

        if (!$user) {
            $respon = [
                'error' => true,
                'status_code' => 401,
                'message' => 'Maaf, username yang Anda gunakan tidak terdaftar',
            ];
            return response()->json($respon, 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            $respon = [
                'error' => true,
                'status_code' => 401,
                'message' => 'Maaf, kata sandi yang Anda gunakan salah',
            ];
            return response()->json($respon, 401);
        }

        $tokenResult = $user->createToken('token-auth')->plainTextToken;
        $respon = [
            'error' => false,
            'status_code' => 200,
            'message' => 'Selamat! Anda berhasil masuk aplikasi',
            'data' => [
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ]
        ];
        return response()->json($respon, 200);
    }
}
