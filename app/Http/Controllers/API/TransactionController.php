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
            $id = $request->input('id');
            $transaction = Transaction::all();

            return ResponseFormatter::success([
                'transaction' => $transaction->load('items.service'),
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
                    'qty' => $service['qty']
                ]);
            }

            return ResponseFormatter::success([
                'transaction' => $transaction->load('items.service'),
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
