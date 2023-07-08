<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        try {
            // $id = $request->input('id');
            $transaction = Transaction::all();

            return ResponseFormatter::success([
                'transaction' => $transaction->load('member', 'cashier', 'items.service'),
            ], 'Transaksi berhasil diambil', 200);
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
            $transaction = Transaction::where('id', $id);
            $x = $transaction->first();
            if ($x) {
                return ResponseFormatter::success([
                    'transaction' => $x
                ], 'Data Transaksi berhasil diambil');
            } else {
                return ResponseFormatter::error([
                    'transaction' => null,
                ], 'Data Transaksi tidak ditemukan', 404);
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'error' => $error,
            ], 'Something went wrong!', 500);
            //throw $th;
        }
    }


    public function checkout(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'exists:services,id',
                'total_price' => 'required'

            ]);

            $transaction = Transaction::create([
                'members_id' => $request->members_id,
                'cashiers_id' => Auth::user()->id,
                'total_price' => $request->total_price,
            ]);



            foreach ($request->items as $service) {
                TransactionItem::create([
                    'transactions_id' => $transaction->id,
                    'services_id' => $service['id'],
                    'sub_price' => $service['sub_price'],
                    'weight' => $service['weight']
                ]);
            }

            return ResponseFormatter::success([
                'transaction' => $transaction->load('member', 'cashier', 'items.service'),
            ], 201, 'Transaksi Berhasil');
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
}
