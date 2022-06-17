<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'icon_path' => 'required',
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


        try {
            DB::beginTransaction();
            $data = [
                'name'  => $request->name,
                'url'  => $request->url,
                'icon_path'  => $request->icon_path,
                'user_id'  => auth()->user()->id
            ];

            $social_link = SocialLink::create($data);
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Data Berhasil Disimpan',
                'data' => $social_link,
            ];
            return response()->json($respon, 200);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            $respon = [
                'error' => true,
                'status_code' => 400,
                'message' => 'Data Gagal Disimpan',
                'data' => $social_link,
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
            'icon_path' => 'required',
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


        try {
            DB::beginTransaction();
            $data = [
                'name'  => $request->name,
                'url'  => $request->url,
                'icon_path'  => $request->icon_path,
                'user_id'  => auth()->user()->id
            ];

            $social_link = SocialLink::find($id);
            $social_link->update($data);
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Data Berhasil Disimpan',
                'data' => $social_link,
            ];
            return response()->json($respon, 200);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            $respon = [
                'error' => true,
                'status_code' => 400,
                'message' => 'Data Gagal Disimpan',
                'data' => $social_link,
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
