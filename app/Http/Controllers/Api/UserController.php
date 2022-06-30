<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getUserProfile($username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            $respon = [
                'error' => true,
                'status_code' => 400,
                'message' => 'Data Tidak Ditemukan',
                'data' => new UserResource($user),
            ];
            return response()->json($respon, 400);
        }
        $respon = [
            'error' => false,
            'status_code' => 200,
            'message' => 'Data Ditemukan',
            'data' => new UserResource($user),
        ];
        return response()->json($respon, 200);
    }
    public function update(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'job_title' => 'required',
            'card_color' => 'required',
            'description' => 'required',
        ]);

        if ($validate->fails()) {
            $respon = [
                'error' => true,
                'status_code' => 401,
                'message' => 'Silahkan Isi Semua Form',
                'messages' => $validate->errors(),
            ];
            return response()->json($respon, 401);
        }

        if ($request->profile_photo_path) {
            if (!$request->hasFile('profile_photo_path')) {
                return response()->json([
                    'error' => true,
                    'message' => 'File not found',
                    'status_code' => 400,
                ], 400);
            }
            $file = $request->file('profile_photo_path');
            if (!$file->isValid()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Image file not valid',
                    'status_code' => 400,
                ], 400);
            }
        }

        $user = User::find(auth()->user()->id);

        try {
            DB::beginTransaction();
            $data = [
                'name'  => $request->name,
                'description'  => $request->description,
                'job_title'  => $request->job_title,
                'card_color'  => $request->card_color,
            ];


            if ($request->profile_photo_path) {
                $file = $request->profile_photo_path->store('upload-card', 'public');
                $data['profile_photo_path'] = $file;
                if (Storage::exists('public/' . $user->profile_photo_path)) {
                    Storage::delete('public/' . $user->profile_photo_path);
                }
            }

            $user->update($data);
            DB::commit();
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Data Berhasil Diperbarui',
                'data' => new UserResource($user),
            ];
            return response()->json($respon, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            $respon = [
                'error' => true,
                'status_code' => 400,
                'message' => 'Data Gagal Diperbarui',
                'dev_message' => $th->getMessage(),
            ];
            return response()->json($respon, 400);
        }
    }

    public function getFileContact($user_id)
    {
        // file vcf berdasarkan user_id
        $user = User::find($user_id);
        $file = $user->vcf_info;

        // download file vcf
        $headers = [
            'Content-Type' => 'text/x-vcard',
            'Content-Disposition' => 'attachment; filename="' . $user->name . '.vcf"',
        ];
        return response()->download(storage_path('app/public/' . $file), $user->name . '.vcf', $headers);
    }

    // update password
    public function updatePassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($request->new_password != $request->confirm_password) {
            $respon = [
                'error' => true,
                'status_code' => 401,
                'message' => 'Password Baru Tidak Sama',
                'messages' => $validate->errors(),
            ];
            return response()->json($respon, 401);
        }

        $user = User::find(auth()->user()->id);
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Password Berhasil Diperbarui',
                'data' => new UserResource($user),
            ];
            return response()->json($respon, 200);
        } else {
            $respon = [
                'error' => true,
                'status_code' => 400,
                'message' => 'Password Lama Salah',
                'data' => new UserResource($user),
            ];
            return response()->json($respon, 400);
        }
    }
}
