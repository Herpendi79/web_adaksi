<?php

namespace App\Http\Controllers;

use App\Models\AnggotaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Transaction;

class TripayCallbackController extends Controller
{
    // Isi dengan private key anda
    protected $privateKey = 'b04nT-QgsBs-XTdRt-FWVw4-EIPlt';

    public function handle(Request $request)
    {
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, $this->privateKey);

        if ($signature !== (string) $callbackSignature) {
            return Response::json([
                'success' => false,
                'message' => 'Invalid signature',
            ]);
        }

        if ('payment_status' !== (string) $request->server('HTTP_X_CALLBACK_EVENT')) {
            return Response::json([
                'success' => false,
                'message' => 'Unrecognized callback event, no action was taken',
            ]);
        }

        $data = json_decode($json);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return Response::json([
                'success' => false,
                'message' => 'Invalid data sent by tripay',
            ]);
        }

        // $invoiceId = $data->reference;
        $tripayReference = $data->reference;
        $status = strtoupper((string) $data->status);

        if ($data->is_closed_payment === 1) {
            $anggota = AnggotaModel::where('order_id', $tripayReference)
                ->first();
            $id_user = $anggota->id_user;

            $invoice = AnggotaModel::where('id_user', $id_user)
                ->where('status_anggota', '=', 'pending')
                ->first();

            if (! $invoice) {
                return Response::json([
                    'success' => false,
                    'message' => 'No invoice found or already paid: ' . $tripayReference,
                ]);
            }

            switch ($status) {
                case 'PAID':
                    $invoice->update(['status_anggota' => 'aktif']);
                    $validasi = new TripayController();
                    $transaction = $validasi->validasiBySnap($id_user);
                    //return redirect('/login')->with('success', 'Akun Anda telah aktif!');
                    break;

                case 'EXPIRED':
                    $invoice->update(['status_anggota' => 'pending']);
                    $hapus = new TripayController();
                    $transaction = $hapus->hapusJikaExpired($id_user);
                    // return redirect('/daftar-anggota-adaksi')->with('error', 'Pembayaran telah expired, silakan daftar ulang.');
                    break;

                case 'FAILED':
                    $invoice->update(['status_anggota' => 'pending']);
                    $hapus = new TripayController();
                    $transaction = $hapus->hapusJikaExpired($id_user);
                   //  return redirect('/daftar-anggota-adaksi')->with('error', 'Pembayaran gagal, silakan daftar ulang.');
                    break;

                default:
                    return Response::json([
                        'success' => false,
                        'message' => 'Unrecognized payment status',
                    ]);
            }

            return Response::json(['success' => true]);
        }
        return response()->json(['status' => 'success']);
    }
}
