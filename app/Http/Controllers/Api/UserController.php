<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'job_title' => 'required',
            'card_color' => 'required',
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
                'data' => $user,
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
}
