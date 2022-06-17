<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContactInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get all data from database
        $contact_infos = ContactInfo::all();
        $respon = [
            'error' => false,
            'status_code' => 200,
            'data' => $contact_infos,
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
                'icon_path'  => $request->icon_path,
                'user_id'  => auth()->user()->id
            ];

            $contact = ContactInfo::create($data);
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Data Berhasil Disimpan',
                'data' => $contact,
            ];
            return response()->json($respon, 200);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            $respon = [
                'error' => true,
                'status_code' => 400,
                'message' => 'Data Gagal Disimpan',
                'data' => $contact,
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
        $contact_info = ContactInfo::find($id);
        if ($contact_info) {
            $respon = [
                'error' => false,
                'status_code' => 200,
                'data' => $contact_info,
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
                'icon_path'  => $request->icon_path,
                'user_id'  => auth()->user()->id
            ];

            $contact = ContactInfo::find($id);
            $contact->update($data);
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Data Berhasil Disimpan',
                'data' => $contact,
            ];
            return response()->json($respon, 200);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            $respon = [
                'error' => true,
                'status_code' => 400,
                'message' => 'Data Gagal Disimpan',
                'data' => $contact,
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
        $contact_info = ContactInfo::find($id);
        if ($contact_info) {
            $contact_info->delete();
            $respon = [
                'error' => false,
                'status_code' => 200,
                'message' => 'Data Berhasil Dihapus',
                'data' => $contact_info,
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
