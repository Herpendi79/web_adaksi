<?php

namespace App\Http\Controllers;

use App\Models\AnggotaModel;
use Illuminate\Http\Request;


class TransactionController extends Controller
{

    public function bayar_anggota_show($reference)
    {

        

      //  return view('guest_page.bayar_anggota_show');
    }

    public function store_pembayaran(Request $request)
    {
        $id_user = $request->id_user;
        $method = $request->method;

        $tripay = new TripayController();
        $transaction = $tripay->requestTransaction($method, $id_user);

        if (!isset($transaction->data->reference)) {
            return back()->with('error', 'Gagal mengambil reference dari Tripay');
        }

        $no_ref = $transaction->data->reference;

        $anggota = AnggotaModel::where('id_user', $id_user)->first();

        if (!$anggota) {
            return back()->with('error', 'Data anggota tidak ditemukan.');
        }

        $anggota->order_id = $no_ref;
        $saved = $anggota->save();

        if (!$saved) {
            return back()->with('error', 'Gagal menyimpan data transaksi.');
        }
        //dd($transaction);

        return redirect()->route('bayar_anggota_show', ['reference' => $no_ref]);
    }
}
