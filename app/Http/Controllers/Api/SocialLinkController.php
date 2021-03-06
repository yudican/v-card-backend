<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SocialLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get all data from database
        $social_links = SocialLink::all();
        $respon = [
            'error' => false,
            'status_code' => 200,
            'data' => $social_links,
        ];
        return response()->json($respon, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'url' => 'required',
            'image_link' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

        if (!$request->hasFile('image_link')) {
            return response()->json([
                'error' => true,
                'message' => 'File not found',
                'status_code' => 400,
            ], 400);
        }
        $file = $request->file('image_link');
        if (!$file->isValid()) {
            return response()->json([
                'error' => true,
                'message' => 'Image file not valid',
                'status_code' => 400,
            ], 400);
        }


        try {
            DB::beginTransaction();
            $file = $request->image_link->store('upload', 'public');
            $data = [
                'name'  => $request->name,
                'url'  => $request->url,
                'icon_path'  => 'default',
                'image_link' => $file,
                'user_id'  => auth()->user()->id
            ];

            $social_link = SocialLink::create($data);
            DB::commit();
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Data Berhasil Disimpan',
                'data' => $social_link,

            ];
            return response()->json($respon, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            $respon = [
                'error' => true,
                'status_code' => 400,
                'message' => 'Data Gagal Disimpan',
                'dev_message' => $th->getMessage(),
            ];
            return response()->json($respon, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $social_link = SocialLink::find($id);
        if ($social_link) {
            $respon = [
                'error' => false,
                'status_code' => 200,
                'data' => $social_link,
            ];
            return response()->json($respon, 200);
        }
        $respon = [
            'error' => true,
            'status_code' => 404,
            'message' => 'Data Tidak Ditemukan',
            'data' => null,
        ];
        return response()->json($respon, 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'url' => 'required',
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

        if ($request->image_link) {
            if (!$request->hasFile('image_link')) {
                return response()->json([
                    'error' => true,
                    'message' => 'File not found',
                    'status_code' => 400,
                ], 400);
            }
            $file = $request->file('image_link');
            if (!$file->isValid()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Image file not valid',
                    'status_code' => 400,
                ], 400);
            }
        }

        try {
            DB::beginTransaction();
            $data = [
                'name'  => $request->name,
                'url'  => $request->url,
                'icon_path'  => $request->icon_path,
                'user_id'  => auth()->user()->id
            ];
            $social_link = SocialLink::find($id);

            if ($request->image_link) {
                $file = $request->image_link->store('upload', 'public');
                $data['image_link'] = $file;
                if (Storage::exists('public/' . $social_link->image_link)) {
                    Storage::delete('public/' . $social_link->image_link);
                }
            }

            $social_link->update($data);
            DB::commit();
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Data Berhasil Disimpan',
                'data' => $social_link,
            ];
            return response()->json($respon, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            $respon = [
                'error' => true,
                'status_code' => 400,
                'message' => 'Data Gagal Disimpan',
                'dev_message' => $th->getMessage(),
            ];
            return response()->json($respon, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $social_link = SocialLink::find($id);
        if ($social_link) {
            if (Storage::exists('public/' . $social_link->image_link)) {
                Storage::delete('public/' . $social_link->image_link);
            }
            $social_link->delete();
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Data Berhasil Dihapus',
                'data' => $social_link,
            ];
            return response()->json($respon, 200);
        }

        $respon = [
            'error' => true,
            'status_code' => 404,
            'message' => 'Data Tidak Ditemukan',
            'data' => null,
        ];
        return response()->json($respon, 404);
    }
}
