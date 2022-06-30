<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();


        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {

            return redirect('login')->with(['error' => 'Silahkan isi semua form yang tersedia']);
        }

        if (!$user) {
            return redirect('login')->with(['error' => 'Username tidak terdaftar']);
        }

        if ($user->role->role_type == 'member') {
            return redirect('login')->with(['error' => 'Maaf, Anda tidak memiliki akses untuk login']);
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return redirect('login')->with(['error' => 'Username atau password salah']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return redirect('login')->with(['error' => 'Unathorized, password yang kamu masukkan tidak sesuai']);
        }


        return redirect('dashboard');
    }
}
