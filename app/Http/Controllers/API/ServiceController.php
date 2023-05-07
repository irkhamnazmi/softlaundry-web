<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function all()
    {
        try {

            $service = Service::all();

            return ResponseFormatter::success([
                'service' => $service,
            ], 'Layanan berhasil diambil', 200);
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

    public function get($id)
    {
        try {
            $service = Service::where('id', $id);
            $x = $service->first();
            if ($x) {
                return ResponseFormatter::success([
                    'service' => $x
                ], 'Data Layanan berhasil diambil');
            } else {
                return ResponseFormatter::error([
                    'service' => null,
                ], 'Data Layanan tidak ditemukan', 404);
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'error' => $error,
            ], 'Something went wrong!', 500);
            //throw $th;
        }
    }


    public function add(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'price' => ['required', 'integer'],
            ]);

            Service::create([
                'name' => $request->name,
                'price' => $request->price,
            ]);


            $service = Service::where('name', $request->name)->first();

            return ResponseFormatter::success(
                [
                    'service' => $service,
                ],
                'Layanan berhasil dibuat',
                201
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    $error,
                    'Ada sesuatu yang salah!',
                    500
                ],
            );
        }
    }

    public function edit(Request $request)
    {
        try {


            $service = Service::where('id', $request->id);
            $service->first();
            if (!empty($service)) {

                $service->update([
                    'name' => $request->name,
                    'price' => $request->price,
                ]);

                return ResponseFormatter::success(
                    [

                        'Service' => $service->first(),
                    ],
                    'Layanan berhasil diubah',
                    200
                );
            } else {
                return ResponseFormatter::error(null, 'Layanan gagal dihapus', 404);
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

            $service = Service::where('id', $id);
            $x = $service->first();

            if (!empty($x)) {

                $service->delete();

                return ResponseFormatter::success(
                    [
                        'Service' => $x,
                    ],
                    'Layanan berhasil dihapus',
                    200
                );
            } else {
                return ResponseFormatter::error([null], 'Layanan gagal dihapus', 404);
            }
        } catch (Exception $error) {
            ResponseFormatter::error([
                $error,
            ], 'Ada sesuatu yang salah!', 500);
        }
    }
}
