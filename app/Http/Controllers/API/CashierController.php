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
                'message' => 'Something went wrong!',
                'error' => $error,
            ], 'Gagal Autentikasi', 500);
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
                'message' => 'Something went wrong!',
                'error' => $error,
            ], 'Gagal Autentikasi', 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->cashier()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }
}
