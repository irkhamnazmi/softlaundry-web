<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Cashier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class CashierController extends Controller
{

    public function all()
    {

        try {

            $cashier = Cashier::all();

            return ResponseFormatter::success([
                'cashier' => $cashier,
            ], 'Data Kasir berhasil diambil', 200);
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

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255', 'unique:cashiers'],
                'phone_number' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', new Password],

            ]);

            Cashier::create([
                'name' => $request->name,
                'address' => $request->address,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]);

            $cashier = Cashier::where('phone_number', $request->phone_number)->first();

            $tokenResult = $cashier->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'cashier' => $cashier
            ], 'Kasir berhasil didaftarkan');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'error' => $error,
            ], 'Something went wrong!', 500);
        }
    }

    public function get($id)
    {
        try {
            $cashier = Cashier::where('id', $id);
            $x = $cashier->first();
            if ($x) {
                return ResponseFormatter::success([
                    'cashier' => $x
                ], 'Data Kasir berhasil diambil');
            } else {
                return ResponseFormatter::error([
                    'cashier' => null,
                ], 'Data Kasir tidak ditemukan', 404);
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'error' => $error,
            ], 'Something went wrong!', 500);
            //throw $th;
        }
    }

    public function profile(Request $request)
    {
        try {
            return ResponseFormatter::success([
                'cashier' => $request->user()
            ], 'Data Kasir berhasil diambil');
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
            $cashier = Cashier::where('id', $request->id);
            $x = $cashier->first();
            if (!empty($request->password)) {
                $pass = Hash::make($request->password);
            } else {
                $pass = $x->password;
            }

            $cashier->update([
                'name' => $request->name,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'roles' => $request->roles,
                'password' => $pass,
            ]);
            return ResponseFormatter::success([
                'cashier' => $cashier->first()
            ], 'Data Kasir berhasil diedit');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'error' => $error,
            ], 'Something went wrong!', 500);
            //throw $th;
        }
    }

    public function delete($id)
    {
        try {

            $cashier = Cashier::where('id', $id);
            $x = $cashier->first();

            if (!empty($x)) {

                $cashier->delete();

                return ResponseFormatter::success(
                    [
                        'Cashier' => $cashier,
                    ],
                    'Data Kasir berhasil dihapus',
                    200
                );
            } else {
                return ResponseFormatter::error([null], 'Kasir gagal dihapus', 404);
            }
        } catch (Exception $error) {
            ResponseFormatter::error([
                $error,
            ], 'Ada sesuatu yang salah!', 500);
        }
    }

    public function login(Request $request)
    {

        try {
            $request->validate([
                'phone_number' => 'required',
                'password' => 'required'
            ]);

            $credentials = request(['phone_number', 'password']);

            // dd($credentials);
            // if (Auth::attempt($credentials)) {
            //     return ResponseFormatter::error([
            //         'message' => 'Unauthorized',
            //     ], 'Authentication Failed', 500);
            // }

            $cashier = Cashier::where('phone_number', $request->phone_number)->first();

            if (!Hash::check($request->password, $cashier->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $cashier->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'cashier' => $cashier
            ], 'Autentikasi Berhasil');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'error' => $error,
            ], 'Something went wrong!', 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->cashier()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }
}
