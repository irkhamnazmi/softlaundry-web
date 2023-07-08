<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Exception;
use Illuminate\Http\Request;

class MemberController extends Controller
{

    public function all()
    {

        try {

            // $member = Member::all();
            $member = Member::where('name', '<>', '-');
            $x = $member->get();

            return ResponseFormatter::success([
                'member' => $x,
            ], 'Member berhasil diambil', 200);
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    $error,
                    'Ada sesuatu yang salah!',
                ],
                'Gagal Autentikasi',
                500
            );
        }
    }

    public function add(Request $request)
    {
        try {
            Member::create([
                'member_id' => $request->member_id,
                'name' => $request->name,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
            ]);

            $member = Member::where('member_id', $request->member_id)->first();

            return ResponseFormatter::success([
                'member' => $member,
            ], 'Member berhasil dibuat', 201);
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    $error,
                ],
                'Ada sesuatu yang salah!',
                500
            );
        }
    }

    public function get($id)
    {
        try {
            $member = Member::where('member_id', $id);
            $x = $member->first();
            if ($x) {
                return ResponseFormatter::success([
                    'member' => $x
                ], 'Data Member berhasil diambil');
            } else {
                return ResponseFormatter::error([
                    'member' => null,
                ], 'Data Member tidak ditemukan', 404);
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'error' => $error,
            ], 'Something went wrong!', 500);
            //throw $th;
        }
    }


    public function edit(Request $request)
    {
        try {

            $member = Member::where('member_id', $request->member_id);
            $x = $member->first();

            if (!empty($x)) {

                $member->update([
                    'name' => $request->name,
                    'address' => $request->address,
                    'phone_number' => $request->phone_number,
                ]);

                return ResponseFormatter::success(
                    [
                        'Member' => $member->first(),
                    ],
                    'Data Member berhasil diubah',
                    200
                );
            } else {
                return ResponseFormatter::error(null, 'Data Member gagal diubah', 404);
            }
        } catch (Exception $error) {
            ResponseFormatter::error(
                [
                    $error,
                ],
                'Ada sesuatu yang salah!',
                500
            );
        }
    }

    public function delete($id)
    {
        try {

            $member = Member::where('id', $id);
            $x = $member->first();

            if (!empty($x)) {

                $member->delete();

                return ResponseFormatter::success(
                    [
                        'Member' => $member,
                    ],
                    'Member berhasil dihapus',
                    200
                );
            } else {
                return ResponseFormatter::error([null], 'Member gagal dihapus', 404);
            }
        } catch (Exception $error) {
            ResponseFormatter::error([
                $error,
            ], 'Ada sesuatu yang salah!', 500);
        }
    }
}
