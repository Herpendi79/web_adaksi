<?php

namespace App\Http\Controllers;

// php spreetsheet
use App\Models\AnggotaModel;
use App\Models\NoAnggotaModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class TripayController extends Controller
{

    protected $defaultPassword;
    public function __construct()
    {

        $password = str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789');
        $pass = substr($password, 0, 8);
        $snap = substr($password, 0, 12);
        $this->defaultPassword = $pass;
    }

    public function getPaymentChannels()
    {
        $apiKey = config('tripay.api_key');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/merchant/payment-channel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response)->data;
        return $response ? $response : $error;
    }


    public function store_anggota(Request $request)
    {
        $messages = [
            'nama_anggota.required' => 'Nama anggota wajib diisi.',
            'nama_anggota.string' => 'Nama anggota harus berupa teks.',
            'nama_anggota.max' => 'Nama anggota maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',

            'nip_nipppk.required' => 'NIP/NIPPPK wajib diisi.',
            'nip_nipppk.string' => 'NIP/NIPPPK harus berupa teks.',
            'nip_nipppk.max' => 'NIP/NIPPPK maksimal 50 karakter.',
            'nip_nipppk.unique' => 'NIP/NIPPPK sudah terdaftar.',

            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.string' => 'Nomor HP harus berupa teks.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar.',

            'status_dosen.required' => 'Status dosen wajib diisi.',
            'status_dosen.string' => 'Status dosen harus berupa teks.',

            'homebase_pt.required' => 'Homebase PT wajib diisi.',
            'homebase_pt.string' => 'Homebase PT harus berupa teks.',
            'homebase_pt.max' => 'Homebase PT maksimal 100 karakter.',

            'provinsi.required' => 'Provinsi wajib diisi.',
            'provinsi.string' => 'Provinsi harus berupa teks.',
            'provinsi.max' => 'Provinsi maksimal 100 karakter.',

        ];

        // validasi NIP

        $nip_nipppk = $request->nip_nipppk;
        $existingAnggota = AnggotaModel::where('nip_nipppk', $nip_nipppk)->where('status_anggota', '!=', 'nonaktif')->first();
        if ($existingAnggota) {
            notify()->error('NIP/NIPPPK sudah terdaftar. Silakan gunakan NIP/NIPPPK yang berbeda.');
            return redirect()->back()->withInput();
        }

        // validasi No HP

        $no_hp = $request->no_hp;
        $existingno_hp = User::where('no_hp', $no_hp)->first();
        if ($existingno_hp) {
            notify()->error('No HP/WA sudah terdaftar. Silakan gunakan HP/WA yang berbeda.');
            return redirect()->back()->withInput();
        }

        // validasi EMail
        $email = $request->email;
        $existingEmail = User::where('email', $email)->first();
        if ($existingEmail) {
            notify()->error('Email sudah terdaftar. Silakan gunakan Email yang berbeda.');
            return redirect()->back()->withInput();
        }

        $request->validate([
            'nama_anggota' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'nip_nipppk' => 'required|string|max:50',
            'no_hp' => 'required|string|max:20',
            'status_dosen' => 'required|string',
            'homebase_pt' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'status_anggota' => 'nullable|in:pending,aktif,nonaktif',
        ], $messages);
        // die;

        try {

            $password = str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789');
            $snap = substr($password, 0, 12);
            $kode_unik = rand(500, 999);
            $biaya = 100000 + $kode_unik;

            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($this->defaultPassword),
                'password_temporary' => $this->defaultPassword,
                'role' => 'anggota',
                'no_hp' => $request->no_hp,
            ]);
            $anggota = AnggotaModel::create([
                'id_user' => $user->id_user,
                'nama_anggota' => $request->nama_anggota,
                'nip_nipppk' => $request->nip_nipppk,
                'status_dosen' => $request->status_dosen,
                'homebase_pt' => $request->homebase_pt,
                'provinsi' => $request->provinsi,
                'biaya' => $biaya,
                'status_anggota' => 'pending',
                'snap' => $snap,
                'order_id' => null,
            ]);

            $urlPembayaran = url('/bayar_anggota/' . $snap);

            $message = "Halo *{$request->nama_anggota}* 👋,

Terima kasih telah mendaftar keanggotaan ADAKSI.

Silakan lakukan pembayaran melalui tautan berikut:
{$urlPembayaran}

Mari bersama berjuang untuk Indonesia Emas! 🇮🇩✨

_Salam,_  
*Tim ADAKSI*";


            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $request->no_hp, // pastikan format nomor sudah internasional (62xxxxx)
                    'message' => $message
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: 5ef8QqtZQtmcBLfiWth5'
                ),
            ));

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                // Jika error, log atau tampilkan
                Log::error('WhatsApp API Error: ' . $error_msg);
            }
            curl_close($curl);

            return redirect()->route('bayar_anggota', ['snapToken' => $snap]);
        } catch (\Exception $e) {
            notify()->error('Terjadi kesalahan saat pendaftaran anggota: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function bayar($snapToken)
    {
        $channels = $this->getPaymentChannels();

        $anggota = AnggotaModel::where('snap', $snapToken)->first();

        return view('guest_page.bayar_anggota', [
            'channels' => $channels,
            'snapToken' => $snapToken,
            'biaya' => $anggota?->biaya ?? 0,
            'nama' => $anggota?->nama_anggota ?? 'Bapak/Ibu Dosen',
            'id_user' => $anggota?->id_user ?? null,
            'order_id' => $anggota?->order_id ?? null,
            'status_anggota' => $anggota?->status_anggota ?? 'tidak_ada',
        ]);
    }

    public function requestTransaction($method, $id_user)
    {

        $apiKey       = config('tripay.api_key');
        $privateKey   = config('tripay.private_key');
        $merchantCode = config('tripay.merchant_code');

        $merchantRef  = 'PX-' . time();

        $user = User::with('anggota')->where('id_user', $id_user)->first();

        if (!$user || !$user->anggota) {
            abort(404, 'User atau Anggota tidak ditemukan');
        }

        $anggota = $user->anggota;

        $data = [
            'method'         => $method,
            'merchant_ref'   => $merchantRef,
            'amount'         => $anggota->biaya,
            'customer_name'  => $anggota->nama_anggota,
            'customer_email' => $user->email,
            'order_items'    => [
                [
                    'name'     => 'Registrasi Anggota',
                    'price'    => $anggota->biaya,
                    'quantity' => 1,
                ]
            ],
            'expired_time' => (time() + (1 * 15 * 60)), // 30 menit
            'signature'    => hash_hmac('sha256', $merchantCode . $merchantRef . $anggota->biaya, $privateKey)
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return (object)[
                'success' => false,
                'message' => $error,
                'data'    => null
            ];
        }

        $responseData = json_decode($response);

        // Pastikan response tidak kosong
        if (!$responseData) {
            return (object)[
                'success' => false,
                'message' => 'Invalid JSON response',
                'data'    => null
            ];
        }

        return $responseData;
    }

    public function detail_transaction($reference)
    {
        $apiKey = config('tripay.api_key');

        $payload = ['reference' => $reference];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/detail?' . http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return (object)[
                'success' => false,
                'message' => $error,
                'data' => null
            ];
        }

        $responseData = json_decode($response);

        // Tambahan: validasi jika response tidak valid
        if (!$responseData || !isset($responseData->data)) {
            return (object)[
                'success' => false,
                'message' => 'Invalid or empty JSON response',
                'data' => null
            ];
        }

        return $responseData->data;
    }

    public function validasiBySnap($id_user)
    {
        // Jika tidak ada lagi id_card tersisa di tabel no_anggota
        $anggota_urut = AnggotaModel::orderBy('id_card', 'desc')->first();

        if (!$anggota_urut) {
            $no_urut = 1;
        } else {
            // Ambil 6 digit terakhir dari id_card terakhir dan +1
            $last_id_card = $anggota_urut->id_card;
            $last_no = (int)substr($last_id_card, -6);
            $no_urut = $last_no + 1;
        }

        // Format ID Card (6 digit belakang)
        if ($no_urut < 10) {
            $id_card = '0000' . $no_urut;
        } elseif ($no_urut < 100) {
            $id_card = '000' . $no_urut;
        } elseif ($no_urut < 1000) {
            $id_card = '00' . $no_urut;
        } elseif ($no_urut < 10000) {
            $id_card = '0' . $no_urut;
        } elseif ($no_urut < 100000) {
            $id_card = '' . $no_urut;
        } else {
            $id_card = (string)$no_urut;
        }

        $id_card = '00119' . $id_card; // Tambahkan prefix


        // Simpan ke data anggota
        $anggota = AnggotaModel::where('id_user', $id_user)->firstOrFail();
        //$anggota->status_anggota = 'aktif';
        $anggota->no_urut = $no_urut;
        $anggota->id_card = $id_card;
        $anggota->save();

        $anggota_user = User::findOrFail($anggota->id_user);
        // $iduser = $anggota->id_user;

        $message = "Halo " . $anggota->nama_anggota . " 👋,\n\n" .
            "Selamat menjadi bagian dari ADAKSI! Akun Anda di sistem telah aktif. Berikut adalah detail akun Anda:\n\n" .
            "🔑 *Username:* " . $anggota_user->email . "\n" .
            "🔒 *Password:* " . $anggota_user->password_temporary . "\n\n" .
            "📝 Login via:\n" . config('app.url') . "\n\n" .
            "✌️ Upload pas photo untuk download KTA.\n\n" .
            "Terima kasih telah bergabung bersama ADAKSI. 🇮🇩✨";

        // Kirim pesan ke semua admin
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $anggota_user->no_hp, // pastikan format nomor sudah internasional (62xxxxx)
                'message' => $message,
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: 5ef8QqtZQtmcBLfiWth5'
            ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            Log::error('WhatsApp API Error: ' . $error_msg);
        }
        curl_close($curl);

        return redirect('/login')->with('success', 'Akun Anda telah aktif!');
    }

    public function hapusJikaExpired($id_user)
    {
        $anggota = AnggotaModel::where('id_user', $id_user)->first();

        if ($anggota) {
            $idUser = $anggota->id_user;
            $anggota->delete();

            User::where('id_user', $idUser)->delete();
        }

        return redirect('/daftar-anggota-adaksi')
            ->with('error', 'Pembayaran telah expired, silakan daftar ulang.')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function cek_status($reference)
    {
        $anggota = AnggotaModel::where('order_id', $reference)->first();

        if (!$anggota) {
            return redirect('/daftar-anggota-adaksi');
        }

        $id_user = $anggota->id_user;
        $status_anggota = $anggota->status_anggota;

        $tripay = new TripayController;
        $detail = $tripay->detail_transaction($reference);

        if ($detail->status === 'paid' && $status_anggota === 'pending') {
            $validasi = new TripayController();
            $transaction = $validasi->validasiBySnap($id_user);
        } elseif ($detail->status === 'paid' && $status_anggota === 'aktif') {
            return redirect('/login')->with('success', 'Akun Anda telah aktif!');
        } else {
            $validasi = new TripayController();
            $transaction = $validasi->hapusJikaExpired($id_user);
        }
        return redirect('/daftar-anggota-adaksi');
    }

    public function status_ajax($reference)
    {
        $tripay = new TripayController;
        $detail = $tripay->detail_transaction($reference);

        if (!$detail || !isset($detail->status)) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status' => $detail->status
        ]);
    }
}
