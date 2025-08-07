<?php

namespace App\Http\Controllers;

// php spreetsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Mail\AnggotaValidateMail;
use App\Models\AnggotaModel;
use App\Models\WebinarModel;
use App\Models\RekeningModel;
use App\Models\AduanModel;
use App\Models\PendaftarRakernasModel;
use App\Models\RakernasModel;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\NoAnggotaModel;
use App\Models\AnggotaExport;
use App\Models\PendaftarExtModel;
use App\Models\RekapPerProvinsiExport;
use App\Models\RekapGabunganExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\SertifikatModel;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Illuminate\Support\Facades\Http;
use Midtrans\Transaction;


class AnggotaController extends Controller
{
    protected $defaultPassword;
    public function __construct()
    {

        $password = str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789');
        $pass = substr($password, 0, 8);
        $this->defaultPassword = $pass;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $anggota = AnggotaModel::where('id_user', $user->id_user)->first();

        $sisaHari = null;

        if ($anggota && $anggota->tgl_keanggotaan) {
            $tanggal_akhir = Carbon::parse($anggota->tgl_keanggotaan)->addDays(365);
            $sisaHari = Carbon::now()->diffInDays($tanggal_akhir, false);
        }

        return view('anggota_page.main.dashboard', compact('anggota', 'sisaHari'));
    }



    public function store(Request $request)
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
            $kode_unik = rand(500, 999);
            $biaya = 105000 + $kode_unik;
            $today = now()->format('Ymd');

            $lastOrder = AnggotaModel::where('order_id', 'like', "INV-{$today}-%")
                ->orderByRaw("CAST(SUBSTRING_INDEX(order_id, '-', -1) AS UNSIGNED) DESC")
                ->first();

            $nextNumber = 1;

            if ($lastOrder) {
                $parts = explode('-', $lastOrder->order_id);
                $lastNumber = (int) end($parts);
                $nextNumber = $lastNumber + 1;
            }

            $order_id = "INV-{$today}-{$nextNumber}";

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
                'order_id' => $order_id,
            ]);

            $durationInMinutes = 30;

            // Set your Merchant Server Key
            \Midtrans\Config::$serverKey = config('midtrans.serverKey');
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = config('midtrans.isProduction');
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = config('midtrans.is3ds');


            $params = array(
                'transaction_details' => array(
                    'order_id' => $order_id,
                    'gross_amount' => $biaya,
                ),
                'customer_details' => array(
                    'first_name' => $request->nama_anggota,
                    'email' => $request->email,
                ),
                'expiry' => array(
                    'start_time' => date("Y-m-d H:i:s O"), // waktu sekarang + zona waktu server
                    'unit' => 'minute', // minute / hour / day
                    'duration' => $durationInMinutes,
                )
            );

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $anggota->snap = $snapToken;
            $anggota->save();

            $urlPembayaran = url('/anggota/pembayaran/' . $snapToken);

            $message = "Halo *{$request->nama_anggota}* 👋,

Terima kasih telah mendaftar keanggotaan ADAKSI.

Silakan lakukan pembayaran melalui tautan berikut:
{$urlPembayaran}

Total tagihan Anda: *Rp " . number_format($biaya, 0, ',', '.') . "*

Mari bersama berjuang untuk Indonesia Emas! 🇮🇩✨

_Salam,_  
*Tim ADAKSI*";


            $message = mb_convert_encoding($message, 'UTF-8', 'auto'); // Pastikan UTF-8

            $data = [
                'target' => $request->no_hp, // Format: 628xxxx
                'message' => $message
            ];

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
                CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE), // penting agar emoji tidak berubah
                CURLOPT_HTTPHEADER => array(
                    'Authorization: 5ef8QqtZQtmcBLfiWth5',
                    'Content-Type: application/json; charset=utf-8'
                ),
            ));

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                Log::error('WhatsApp API Error: ' . $error_msg);
            }

            curl_close($curl);


            return redirect()->route('anggota.pembayaran', ['snapToken' => $snapToken]);
        } catch (\Exception $e) {
            notify()->error('Terjadi kesalahan saat pendaftaran anggota: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function cekDanHapusJikaExpired($snapToken)
    {
        $anggota = \App\Models\AnggotaModel::where('snap', $snapToken)->first();

        if (!$anggota) {
            return response()->json(['status' => 'not_found']);
        }

        $order_id = $anggota->order_id;

        $serverKey = config('midtrans.serverKey');
        $base64 = base64_encode($serverKey . ':');

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $base64
        ])->get("https://api.midtrans.com/v2/{$order_id}/status");

        if ($response->ok()) {
            $status = $response->json()['transaction_status'];

            if ($status === 'expire') {
                // Panggil fungsi penghapusan
                return $this->hapusJikaExpired($snapToken);
            }

            return response()->json(['status' => $status]);
        }

        return response()->json(['error' => 'Gagal cek status'], 500);
    }

    public function hapusJikaExpired($snapToken)
    {
        $anggota = \App\Models\AnggotaModel::where('snap', $snapToken)->first();

        if ($anggota) {
            $idUser = $anggota->id_user;
            $anggota->delete();

            // Hapus user juga
            \App\Models\User::where('id_user', $idUser)->delete();

            return redirect('/daftar-anggota')->with('error', 'Pembayaran telah expired, silakan daftar ulang.');
        }

        return redirect('/daftar-anggota')->with('error', 'Data tidak ditemukan.');
    }

    public function callback(Request $request)
    {
        // Log awal agar bisa dilihat di laravel.log
        Log::info('Midtrans callback hit', $request->all());

        $serverKey = config('midtrans.serverKey');

        // Validasi signature
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            Log::warning('Signature not valid', [
                'expected' => $hashed,
                'received' => $request->signature_key
            ]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Ambil data anggota berdasarkan order_id
        $anggota = AnggotaModel::where('order_id', $request->order_id)->first();

        if (!$anggota) {
            Log::error('Anggota not found', ['order_id' => $request->order_id]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Eksekusi jika status transaksi expired
        if ($request->transaction_status === 'expired') {
            $idUser = $anggota->id_user;

            $anggota->delete();
            User::where('id_user', $idUser)->delete();

            // Jangan redirect dari callback Midtrans
            // Balas saja status 200 OK
            return response()->json(['message' => 'Order expired and deleted'], 200);
        }

        // Tambahkan logika lain untuk "capture", "settlement", dsb jika perlu

        return response()->json(['message' => 'Callback received'], 200);
    }

    public function cekDanHapusJikaExpiredRakernas($snapToken)
    {
        $pendaftar = \App\Models\PendaftarRakernasModel::where('snap', $snapToken)->first();

        if (!$pendaftar) {
            return response()->json(['status' => 'not_found']);
        }

        $order_id = $pendaftar->order_id;

        $serverKey = config('midtrans.serverKey');
        $base64 = base64_encode($serverKey . ':');

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $base64
        ])->get("https://api.midtrans.com/v2/{$order_id}/status");

        if ($response->ok()) {
            $status = $response->json()['transaction_status'];

            if ($status === 'expire') {
                $pendaftar->delete();
                return response()->json(['status' => 'deleted']);
            }

            return response()->json(['status' => $status]);
        }

        return response()->json(['error' => 'Gagal cek status'], 500);
    }


    public function showAll(Request $request)
    {
        $user = Auth::user();
        $anggota = AnggotaModel::with('user')
            ->where('anggota.status_anggota', 'aktif') // Filter utama: wajib 'pending'
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('nama_anggota', 'like', '%' . $request->search . '%')
                        ->orWhere('nip_nipppk', 'like', '%' . $request->search . '%')
                        ->orWhere('no_hp', 'like', '%' . $request->search . '%')
                        ->orWhere('homebase_pt', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('id_card', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status_anggota, function ($query) use ($request) {
                $query->where('status_anggota', $request->status_anggota);
            })
            ->join('users', 'anggota.id_user', '=', 'users.id_user')
            ->where('anggota.status_anggota', '=', 'aktif')
            ->orderBy('anggota.nama_anggota', 'asc')
            ->paginate(10);
        return view($user->role . '_page.anggota.index', compact('anggota'));
    }

    public function showAllCalonAnggota(Request $request)
    {
        // $calon = AnggotaModel::where('status_anggota', 'pending')->paginate(10); // 10 = jumlah per halaman
        $user = Auth::user();
        $calon = AnggotaModel::with('user')
            ->where('anggota.status_anggota', 'pending') // Filter utama: wajib 'pending'
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('nama_anggota', 'like', '%' . $request->search . '%')
                        ->orWhere('nip_nipppk', 'like', '%' . $request->search . '%')
                        ->orWhere('no_hp', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status_anggota, function ($query) use ($request) {
                $query->where('status_anggota', $request->status_anggota);
            })
            ->join('users', 'anggota.id_user', '=', 'users.id_user')
            ->where('anggota.status_anggota', '=', 'pending')
            ->orderBy('anggota.nama_anggota', 'asc')
            ->paginate(10);

        return view('admin_page.calonanggota.index', compact('calon'));
    }

    //import anggota
    public function ImportAnggota()
    {
        $calon = AnggotaModel::where('status_anggota', 'aktif')->get();
        return view('admin_page.importanggota.index', compact('calon'));
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathName());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Lewati baris pertama jika header
        foreach (array_slice($rows, 1) as $row) {
            User::create([
                'name'     => $row[0],
                'email'    => $row[1],
                'password' => bcrypt($row[2]),
            ]);
        }

        return redirect()->back()->with('success', 'Data berhasil diimport!');
    }

    //rekap anggota
    public function RekapAnggota(Request $request)
    {
        $query = AnggotaModel::select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as total'))
            ->where('status_anggota', 'aktif')
            ->whereNotNull('bukti_tf_pendaftaran')
            ->where('bukti_tf_pendaftaran', '!=', '');

        // Hitung tanggal berdasarkan input atau default bulan berjalan
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // ✅ Default: bulan berjalan
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        $query->whereBetween('created_at', [$start, $end]);

        $rekap = $query->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal', 'asc')
            ->get();

        $totalNominal = $rekap->sum(fn($item) => $item->total * 100000);

        return view('admin_page.rekap.index', [
            'rekap' => $rekap,
            'totalNominal' => $totalNominal,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
        ]);
    }


    public function tampilAnggota(Request $request)
    {
        // Ambil no_urut terakhir
        $noUrutTerakhir = AnggotaModel::count();


        // Ambil nama view dari query string (misalnya ?page=welcome)
        $halaman = $request->query('page', 'welcome');

        // Ubah nama menjadi path view
        if ($halaman == 'welcome') {
            $view = 'welcome';
        } elseif ($halaman == 'info') {
            $view = 'guest_page.info-munas-pertama';
        } else {
            abort(404, 'Halaman tidak ditemukan');
        }

        return view($view, compact('noUrutTerakhir'));
    }

    // edit anggota
    public function edit($id)
    {
        $anggota = AnggotaModel::with('user')
            ->where('anggota.id_anggota', $id)
            ->join('users', 'anggota.id_user', '=', 'users.id_user')
            ->firstOrFail();
        // pastikan anggota yang diambil adalah anggota yang dimiliki oleh user yang sedang login
        return view('admin_page.anggota.edit', compact('anggota'));
    }



    public function update(Request $request)
    {
        $user = User::findOrFail($request->id_user);
        $anggota = AnggotaModel::findOrFail($request->id_anggota);

        $request->validate([
            'id_user' => 'required|integer|exists:users,id_user',
            'id_anggota' => 'required|integer|exists:anggota,id_anggota',
            'nama_anggota' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id_user . ',id_user',
            // 'nip_nipppk' => 'nullable|string|max:50|unique:anggota,nip_nipppk,' . $anggota->id_anggota . ',id_anggota',
            'no_hp' => 'required|string|max:20',
            'status_dosen' => 'required|string',
            'homebase_pt' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            // 'keterangan' => 'string|max:255',
        ]);
        try {
            $user->update(['email' => $request->email]);
            $anggota->update([
                'nama_anggota' => $request->nama_anggota,
                'nip_nipppk' => $request->nip_nipppk,
                'no_hp' => $request->no_hp,
                'status_dosen' => $request->status_dosen,
                'homebase_pt' => $request->homebase_pt,
                'provinsi' => $request->provinsi,
                'keterangan' => $request->keterangan,
            ]);

            // ubah no hp dari user
            $user->no_hp = $request->no_hp;
            $user->save();


            return redirect()->back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }


    public function validasiBySnap($snapToken)
    {

        $idCardTersedia = NoAnggotaModel::orderBy('id_card', 'asc')->first();

        if ($idCardTersedia) {
            // Gunakan id_card dari no_anggota dan ambil 6 digit terakhir untuk no_urut
            $id_card = $idCardTersedia->id_card;
            $no_urut = (int)substr($id_card, -6);

            // Hapus data id_card dari tabel no_anggota setelah digunakan
            $idCardTersedia->delete();
        } else {
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
        }

        // Simpan ke data anggota
        $anggota = AnggotaModel::where('snap', $snapToken)->firstOrFail();
        $anggota->status_anggota = 'aktif';
        $anggota->no_urut = $no_urut;
        $anggota->id_card = $id_card;
        $anggota->save();

        $anggota_user = User::findOrFail($anggota->id_user);
        $iduser = $anggota->id_user;

        $message = "Halo " . $anggota->nama_anggota . " 👋,\n\n" .
            "Selamat menjadi bagian dari ADAKSI! Akun Anda di sistem telah aktif. Berikut adalah detail akun Anda:\n\n" .
            "🔑 *Username:* " . $anggota_user->email . "\n" .
            "🔒 *Password:* " . $anggota_user->password_temporary . "\n\n" .
            "📝 Login via:\n" . config('app.url') . "\n\n" .
            "✌️ Upload pas photo untuk download KTA.\n\n" .
            "Terima kasih telah bergabung bersama ADAKSI. 🇮🇩✨";

        $message = mb_convert_encoding($message, 'UTF-8', 'auto'); // Pastikan UTF-8 tetap

        $data = [
            'target' => $anggota_user->no_hp,
            'message' => $message,
        ];

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
            CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => array(
                'Authorization: 5ef8QqtZQtmcBLfiWth5',
                'Content-Type: application/json; charset=utf-8',
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

    public function downloadKTA_1()
    {
        $data = [
            'foto' => 'foto.jpg' // ganti sesuai nama file foto
        ];


        // $pdf = Pdf::loadView('components.kartu_tanda_anggota', $data);
        // return $pdf->stream('kta-anggota.pdf');
    }

    public function downloadKTA()
    {
        $anggota = Auth::user()->anggota;
        if ($anggota->status_anggota !== 'aktif') {
            return redirect()->back()->with('error', 'Kartu Tanda Anggota hanya dapat diunduh oleh anggota yang sudah aktif.');
        }

        $masaBerlakuSampai = Carbon::parse($anggota->tgl_keanggotaan)->addDays(365)->format('d-m-Y');

        $data = [
            'foto' => $anggota->foto ?? 'foto.jpg', // ganti sesuai nama file foto
            'nama_anggota' => $anggota->nama_anggota,
            'nip_nipppk' => $anggota->nip_nipppk,
            'id_card' => $anggota->id_card,
            'created_at' => $anggota->created_at->format('d-m-Y'),
            'masa_berlaku_sampai' => $masaBerlakuSampai,
        ];

        return view('components.kartu_tanda_anggota', compact('data'));
        // $pdf = Pdf::loadView('components.kartu_tanda_anggota', $data)->setPaper('a4', 'portrait');
        // return $pdf->stream('kta-anggota.pdf');
    }

    public function editProfile()
    {
        $user = Auth::user()->load('anggota');
        return view('anggota_page.profile.form', compact('user'));
    }

    public function profile()
    {
        $user = Auth::user()->load('anggota');
        return view('anggota_page.profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail($request->id_user);
        $anggota = AnggotaModel::findOrFail($request->id_anggota);

        $request->validate([
            'id_user' => 'required|integer|exists:users,id_user',
            'id_anggota' => 'required|integer|exists:anggota,id_anggota',
            'nama_anggota' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id_user . ',id_user',
            'nip_nipppk' => 'required|string|max:50|unique:anggota,nip_nipppk,' . $anggota->id_anggota . ',id_anggota',
            'no_hp' => 'required|string|max:20',
            'status_dosen' => 'required|string',
            'homebase_pt' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);
        // cek data foto
        if ($request->hasFile('foto')) {
            $request->validate([
                'foto' => 'file|mimes:jpg,jpeg,png|max:2048',
            ]);
        }
        try {
            $user->update(['email' => $request->email]);
            $anggota->update([
                'nama_anggota' => $request->nama_anggota,
                'nip_nipppk' => $request->nip_nipppk,
                'no_hp' => $request->no_hp,
                'status_dosen' => $request->status_dosen,
                'homebase_pt' => $request->homebase_pt,
                'provinsi' => $request->provinsi,
                'foto' => $request->hasFile('foto') ? $this->uploadFile($request->file('foto'), 'uploads/foto_anggota') : $anggota->foto,
            ]);

            // ubah no hp dari user
            $user->no_hp = $request->no_hp;
            $user->save();


            return redirect()->route('anggota.profile')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function editPassword()
    {
        $user = Auth::user()->load('anggota');
        return view('anggota_page.profile.edit_password', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:8',
            'konfirmasi_password' => 'required|string|min:8|same:password_baru',
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 8 karakter.',
            'konfirmasi_password.required' => 'Konfirmasi password wajib diisi.',
            'konfirmasi_password.same' => 'Konfirmasi password harus sama dengan password baru.',
        ]);

        if (!password_verify($request->password_lama, $user->password)) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        try {
            $user->update([
                'password' => bcrypt($request->password_baru),
                'password_temporary' => null, // set password temporary ke null
            ]);
            return redirect()->route('anggota.profile')->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update password: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new AnggotaExport, 'data-anggota.xlsx');
    }

    public function TabulasiAnggota()
    {
        // Ambil data: grup berdasarkan provinsi dan homebase_pt
        $data = DB::table('anggota')
            ->select('provinsi', 'homebase_pt', DB::raw('COUNT(*) as jumlah'))
            ->where('status_anggota', 'aktif')
            ->groupBy('provinsi', 'homebase_pt')
            ->orderBy('provinsi')
            ->orderBy('homebase_pt')
            ->get();

        // Kelompokkan berdasarkan provinsi
        $grouped = [];
        foreach ($data as $row) {
            $grouped[$row->provinsi]['data'][] = [
                'pt' => $row->homebase_pt,
                'jumlah' => $row->jumlah
            ];
            $grouped[$row->provinsi]['subtotal'] =
                ($grouped[$row->provinsi]['subtotal'] ?? 0) + $row->jumlah;
        }

        return view('admin_page.tabulasi.index', compact('grouped'));
    }

    public function TabulasiAnggotaPage()
    {
        $data = DB::table('anggota')
            ->select('provinsi', 'homebase_pt', DB::raw('COUNT(*) as jumlah'))
            ->where('status_anggota', 'aktif')
            ->groupBy('provinsi', 'homebase_pt')
            ->orderBy('provinsi')
            ->orderBy('homebase_pt')
            ->get();

        $grouped = [];
        $rekapProvinsi = [];

        foreach ($data as $row) {
            // Kelompokkan ke dalam grup
            $grouped[$row->provinsi]['data'][] = [
                'pt' => $row->homebase_pt,
                'jumlah' => $row->jumlah
            ];

            // Hitung subtotal per provinsi
            $grouped[$row->provinsi]['subtotal'] =
                ($grouped[$row->provinsi]['subtotal'] ?? 0) + $row->jumlah;
        }

        // Ambil total per provinsi (rekap) dan urutkan
        foreach ($grouped as $provinsi => $g) {
            $rekapProvinsi[$provinsi] = $g['subtotal'];
        }

        ksort($rekapProvinsi); // Urutkan berdasarkan abjad provinsi

        $provinsiKeys = [
            'Aceh' => 'id-ac',
            'Sumatera Utara' => 'id-su',
            'Sumatera Barat' => 'id-sb',
            'Riau' => 'id-ri',
            'Kepulauan Riau' => 'id-kr',
            'Jambi' => 'id-ja',
            'Sumatera Selatan' => 'id-ss',
            'Bangka Belitung' => 'id-bb',
            'Bengkulu' => 'id-be',
            'Lampung' => 'id-la',

            'DKI Jakarta' => 'id-jk',
            'Jawa Barat' => 'id-jb',
            'Banten' => 'id-bt',
            'Jawa Tengah' => 'id-jt',
            'DI Yogyakarta' => 'id-yo',
            'Jawa Timur' => 'id-ji',

            'Bali' => 'id-ba',
            'Nusa Tenggara Barat' => 'id-nb',
            'Nusa Tenggara Timur' => 'id-nt',

            'Kalimantan Barat' => 'id-kb',
            'Kalimantan Tengah' => 'id-kt',
            'Kalimantan Selatan' => 'id-ks',
            'Kalimantan Timur' => 'id-ki',
            'Kalimantan Utara' => 'id-ku',

            'Sulawesi Utara' => 'id-sa',
            'Gorontalo' => 'id-go',
            'Sulawesi Tengah' => 'id-st',
            'Sulawesi Barat' => 'id-sr',
            'Sulawesi Selatan' => 'id-sg',
            'Sulawesi Tenggara' => 'id-se',

            'Maluku' => 'id-ma',
            'Maluku Utara' => 'id-mu',

            'Papua' => 'id-pa',
            'Papua Barat' => 'id-pb',
            'Papua Barat Daya' => 'id-py',
            'Papua Tengah' => 'id-pe',
            'Papua Pegunungan' => 'id-pg',
            'Papua Selatan' => 'id-ps'
        ];

        $provinsiAktif = DB::table('anggota')
            ->where('status_anggota', 'aktif')
            ->distinct()
            ->pluck('provinsi')
            ->toArray();

        $filteredProvinsiKeys = array_filter($provinsiKeys, function ($key) use ($provinsiAktif) {
            return in_array($key, $provinsiAktif);
        }, ARRAY_FILTER_USE_KEY);

        $mapData = [];
        foreach ($filteredProvinsiKeys as $provinsi => $hcKey) {
            $mapData[] = [
                'hc-key' => $hcKey,
                'value' => $rekapProvinsi[$provinsi] ?? 0
            ];
        }


        return view('anggota_page.tabulasi.index', compact('grouped', 'rekapProvinsi', 'mapData'));
    }



    public function exportRekapPerProvinsi()
    {
        return Excel::download(new RekapPerProvinsiExport, 'rekap-per-provinsi.xlsx');
    }

    public function exportGabungan()
    {
        return Excel::download(new RekapGabunganExport, 'rekap-adaksi.xlsx');
    }

    public function webinar(Request $request)
    {
        // Ambil status anggota user (aktif/nonaktif) dari tabel anggota
        // Ambil email & no_hp user yang sedang login
        $user = Auth::user();
        $email = $user->email ?? null;
        $no_hp = $user->no_hp ?? null;

        // Ambil id_wb yang sudah terdaftar dengan email / no_hp user ini
        $webinar_terdaftar = DB::table('pendaftar_webinar_ext')
            ->where(function ($query) use ($email, $no_hp) {
                $query->where('email', $email)
                    ->Where('no_hp', $no_hp);
            })
            ->pluck('id_wb')
            ->unique()
            ->toArray();

        $status_anggota = DB::table('anggota')
            ->where('id_user', $user->id_user)
            ->value('status_anggota');


        // Tentukan tipe biaya yang akan digunakan berdasarkan status anggota
        $biaya_tipe = ($status_anggota === 'aktif') ? 'biaya_anggota_aktif' : 'biaya_anggota_non_aktif';

        $webinar = WebinarModel::with('fasilitas', 'rekening')
            ->select(
                'id_wb',
                'judul',
                $biaya_tipe . ' as biaya',
                'deskripsi',
                'hari',
                'tanggal_mulai',
                'tanggal_selesai',
                'pukul',
                'sertifikat_depan',
                'sertifikat_belakang',
                'link_zoom',
                'bayar_free',
                'moderator',
                'flyer',
                'status',
                'id_rek'
            )
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('judul', 'like', '%' . $request->search . '%')
                        ->orWhere('tanggal_mulai', 'like', '%' . $request->search . '%')
                        ->orWhere('moderator', 'like', '%' . $request->search . '%')
                        ->orWhere('hari', 'like', '%' . $request->search . '%');
                });
            })
            ->where('status', '!=', 'draft')
            // ->where($biaya_tipe, '>', 0) // hanya ambil data yang memiliki biaya
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);

        $webinar->getCollection()->transform(function ($item) {
            $kode_unik = rand(100, 999);
            $item->biaya_unik = ($item->biaya ?? 0) + $kode_unik;
            return $item;
        });

        return view('anggota_page.webinar.index', compact('webinar', 'webinar_terdaftar', 'biaya_tipe'));
    }

    public function uploadFilePendaftar($file, $path, $prefix = '')
    {
        $filename = $prefix . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Ganti dengan path absolut sesuai lokasi sebenarnya
        $destination = base_path('../public_html/' . $path);

        if (!is_dir($destination) && !mkdir($destination, 0755, true)) {
            // fallback jika gagal
            $destination = base_path('public/' . $path);
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
        }


        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        try {
            $file->move($destination, $filename);
        } catch (\Exception $e) {
            dd('Gagal simpan file: ' . $e->getMessage());
        }

        return $filename;
    }

    public function store_registrasi_anggota(Request $request)
    {
        $id_wb = $request->id_wb;
        $webinar = WebinarModel::findOrFail($id_wb);
        if ($webinar->bayar_free === 'bayar') {
            $messages = [
                'keterangan.required' => 'Wajib diisi.',
                'keterangan.string' => 'Harus berupa teks.',
                'keterangan.max' => 'Maksimal 250 karakter.',

                'bukti_transfer.required' => 'Bukti transfer wajib diunggah.',
                'bukti_transfer.file' => 'Bukti transfer harus berupa file.',
                'bukti_transfer.mimes' => 'Bukti transfer harus berupa file JPG, JPEG, PNG, atau PDF.',
                'bukti_transfer.max' => 'Ukuran bukti transfer maksimal 2MB.',
            ];

            $request->validate([
                'keterangan' => 'required|string|max:255',
                'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ], $messages);
        } else {
            $request->validate([
                'keterangan' => 'nullable|string|max:255',
                'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);
        }


        try {
            $id_wb = $request->id_wb;
            $biaya = $request->biaya;
            $keterangan = $request->keterangan;
            $id_user = $request->id_user ?? Auth::id(); // fallback jika id_user belum dikirim

            // Ambil data user + anggota
            $data = DB::table('users')
                ->join('anggota', 'users.id_user', '=', 'anggota.id_user')
                ->select(
                    'anggota.nama_anggota',
                    'users.email',
                    'users.no_hp',
                    'anggota.nip_nipppk',
                    'anggota.status_dosen',
                    'anggota.homebase_pt',
                    'anggota.provinsi'
                )
                ->where('users.id_user', $id_user)
                ->first();

            if (!$data) {
                return back()->with('error', 'Data user/anggota tidak ditemukan.');
            }

            // Ambil data webinar untuk validasi keberadaan
            $webinar = WebinarModel::findOrFail($id_wb);

            // Upload file bukti transfer
            $bukti_tf_path = null;
            if ($request->hasFile('bukti_transfer')) {
                $bukti_tf_path = $this->uploadFilePendaftar(
                    $request->file('bukti_transfer'),
                    'uploads/bukti_tf_pendaftaran'
                );
            }
            if ($webinar->bayar_free === 'bayar') {
                // Simpan ke PendaftarExtModel
                PendaftarExtModel::create([
                    'id_wb' => $id_wb,
                    'nama' => $data->nama_anggota ?? '-',
                    'email' => $data->email ?? '-',
                    'no_hp' => $data->no_hp ?? '-',
                    'nip' => $data->nip_nipppk ?? '-',
                    'status' => $data->status_dosen ?? '-',
                    'home_base' => $data->homebase_pt ?? '-',
                    'provinsi' => $data->provinsi ?? '-',
                    'biaya' => $biaya ?? 0,
                    'keterangan' => $keterangan,
                    'bukti_tf' => $bukti_tf_path,
                    'token' => null,
                    'no_urut' => null,
                    'no_sertifikat' => null,
                ]);

                $message = "🔔 *Pemberitahuan Pendaftaran Kegiatan* 🔔

Halo Admin 👋,
            
Telah terdaftar peserta baru kegiatan di sistem ADAKSI yang menunggu proses validasi. Berikut detailnya:
            
👤 *Nama:* " . $data->nama_anggota . "
📧 *Email:* " . $data->email . "
📱 *No. HP:* " . $data->no_hp . "
🏢 *Instansi:* " . $data->homebase_pt . "
🌍 *Provinsi:* " . $data->provinsi . "
            
📌 *Status Kepesertaan:* Pending
        
Mohon untuk segera melakukan validasi dalam 2x24 jam melalui halaman admin berikut:
" . config('app.url') . "
Silakan login menggunakan akun admin Anda.
            
Terima kasih atas kerjasamanya.
            
Salam,  
*Sistem ADAKSI*";


                // get all users with role admin
                $users = User::whereIn('role', ['admin'])->get();

                // Kirim pesan ke semua adminn
                foreach ($users as $user) {
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
                            'target' => $user->no_hp, // pastikan format nomor sudah internasional (62xxxxx)
                            'message' => $message,
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
                }

                // Kirim pesan ke user
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
                        'target' => $data->no_hp, // pastikan format nomor sudah internasional (62xxxxx)
                        'message' => "Halo " . $data->nama_anggota . "👋,\n\n" .
                            "Terima kasih telah mendaftar *" . $webinar->judul . "*" .
                            ". Pendaftaran Anda sedang dalam *proses validasi* oleh admin. Silakan tunggu dalam waktu maksimal *2x24* jam.\n\n" .
                            "Jika ada pertanyaan, Anda dapat menghubungi admin melalui email atau nomor WhatsApp yang tertera di website.\n\n" .
                            "Salam,\n*Sistem ADAKSI*",
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
            } else {

                $token = Str::random(8);

                $id_wb = $request->id_wb;

                $sertifikat = SertifikatModel::where('id_wb', $id_wb)->firstOrFail();

                // Ambil no_surat dari sertifikat
                $no_surat = $sertifikat->no_surat;

                // Cari pendaftar dengan no_sertifikat yang LIKE no_surat yang sedang diproses
                $lastDaftar = PendaftarExtModel::where('no_sertifikat', 'like', $no_surat . '%')
                    ->orderBy('no_urut', 'desc')
                    ->first();

                // Jika ditemukan, no_urut = terakhir + 1
                // Jika tidak ditemukan (nomor baru), no_urut = 1
                $no_urut = $lastDaftar ? $lastDaftar->no_urut + 1 : 1;

                // Romawi bulan
                $bulan_romawi = [
                    1 => 'I',
                    2 => 'II',
                    3 => 'III',
                    4 => 'IV',
                    5 => 'V',
                    6 => 'VI',
                    7 => 'VII',
                    8 => 'VIII',
                    9 => 'IX',
                    10 => 'X',
                    11 => 'XI',
                    12 => 'XII',
                ];

                $bulan = date('n');
                $tahun = date('Y');
                $bulan_rom = $bulan_romawi[$bulan];
                $hasil = $bulan_rom . '/' . $tahun;

                // Format no_sertifikat
                $no_sertifikat = $sertifikat->no_surat . '-(' . $no_urut . ')/' . $sertifikat->angkatan . '/SERT' . '/' . $sertifikat->unit . '-ADAKSI/' . $hasil;
                $webinar = WebinarModel::findOrFail($request->id_wb);

                // Simpan ke PendaftarExtModel
                PendaftarExtModel::create([
                    'id_wb' => $id_wb,
                    'nama' => $data->nama_anggota ?? '-',
                    'email' => $data->email ?? '-',
                    'no_hp' => $data->no_hp ?? '-',
                    'nip' => $data->nip_nipppk ?? '-',
                    'status' => $data->status_dosen ?? '-',
                    'home_base' => $data->homebase_pt ?? '-',
                    'provinsi' => $data->provinsi ?? '-',
                    'biaya' => null,
                    'keterangan' => null,
                    'bukti_tf' => null,
                    'token' => $token,
                    'no_urut' => $no_urut,
                    'no_sertifikat' => $no_sertifikat,
                ]);

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

                        'target' => $data->no_hp, // pastikan format nomor sudah internasional (62xxxxx)
                        'message' => "Halo " . $data->nama_anggota . "👋,\n\n" .
                            "Terima kasih telah mendaftar *" . $webinar->judul . "*." .
                            "\n\nSetelah kegiatan selesai anda dapat download sertifikat dan fasilitas lainnya di akun Anda masing-masing, tepatnya di menu *Webinar-> Klik Download Fasilitas*.\n\n" .
                            "Jika ada pertanyaan, Anda dapat menghubungi admin melalui email atau nomor WhatsApp yang tertera di website.\n\n" .
                            "Salam,\n*Sistem ADAKSI*",
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
            }



            return redirect()->back()->with('success', 'Pendaftaran kegiatan berhasil, periksa WA anda untuk update selanjutnya. Salam ADAKSI!');
        } catch (\Exception $e) {
            Log::error('Error Pendaftaran Webinar: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat pendaftaran: ' . $e->getMessage());
        }
    }



    public function showAllRakernas(Request $request)
    {
        $rakernas = RakernasModel::with('rekening_rakernas')
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('tema', 'like', '%' . $request->search . '%')
                        ->orWhere('tanggal_mulai', 'like', '%' . $request->search . '%')
                        ->orWhere('tempat', 'like', '%' . $request->search . '%')
                        ->orWhere('tanggal_selesai', 'like', '%' . $request->search . '%');
                });
            })
            ->with(['pendaftar' => function ($q) {
                $q->where('id_user', Auth::id());
            }])
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);

        $rakernas->getCollection()->transform(function ($item) {
            $kode_unik = rand(100, 999);
            $item->biaya_unik_pengurus = ($item->biaya ?? 0) + $kode_unik;
            $item->biaya_unik_non_pengurus = ($item->biaya_non_pengurus ?? 0) + $kode_unik;
            $item->sisa_kuota = $item->hitungSisaKuota();
            $item->limit_daftar = $item->hitungLimitDaftar();
            $item->valid_daftar = $item->hitungPendaftarValid();
            $item->unvalid_daftar = $item->hitungPendaftarUnValid();
            return $item;
        });

        $id_user = Auth::id();

        // Ambil semua id_rk yang sudah didaftarkan user ini
        $rakernas_terdaftar_ids = PendaftarRakernasModel::where('id_user', $id_user)
            ->pluck('id_rk')
            ->toArray();

        $rakernas_status = PendaftarRakernasModel::where('id_user', $id_user)
            ->where('status', 'valid')
            ->pluck('id_rk')
            ->toArray();

        $anggota = AnggotaModel::where('id_user', $id_user)->first();


        return view('anggota_page.rakernas.index', compact('rakernas', 'rakernas_terdaftar_ids', 'rakernas_status', 'anggota'));
    }

    public function store_registrasi_rakernas(Request $request)
    {
        $kepengurusan = $request->kepengurusan;
        $rules = [
            'kepengurusan' => 'required|string|max:255',
        ];

        // Validasi ukuran baju hanya jika BUKAN Anggota Biasa
        if ($kepengurusan !== 'Anggota Biasa') {
            $rules['ukuran_baju'] = 'required|string|max:255';
        }

        $messages = [
            'kepengurusan.required' => 'Wajib dipilih.',
            'ukuran_baju.required' => 'Wajib dipilih.',
        ];

        $request->validate($rules, $messages);


        try {


            // Set your Merchant Server Key
            \Midtrans\Config::$serverKey = config('midtrans.serverKey');
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = false;
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = true;
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = true;

            $id_rk = $request->id_rk;
            $biaya = $request->kepesertaan_hidden;
            $ukuran_baju = $request->ukuran_baju;
            $kepengurusan = $request->kepengurusan;
            $id_user = $request->id_user ?? Auth::id(); // fallback jika id_user belum dikirim

            // Override nilai jika Anggota Biasa
            if ($kepengurusan === 'Anggota Biasa') {
                $ukuran_baju = '-';
            }

            // Ambil data user + anggota
            $data = User::with('anggota')->where('id_user', $id_user)->first();

            if (!$data) {
                return back()->with('error', 'Data user/anggota tidak ditemukan.');
            }

            // Ambil data webinar untuk validasi keberadaan
            $rakernas = RakernasModel::findOrFail($id_rk);

            // ✅ Validasi: cek apakah user sudah mendaftar sebelumnya
            $existing = PendaftarRakernasModel::where('id_rk', $id_rk)
                ->where('id_user', $id_user)
                ->exists();

            if ($existing) {
                return back()->with('error', 'Anda sudah terdaftar dalam Rakernas ini.');
            }

            $order_id = random_int(1000000000, 9999999999);
            $durationInMinutes = 10;

            $params = array(
                'transaction_details' => array(
                    'order_id' => $order_id,
                    'gross_amount' => $biaya,
                ),
                'customer_details' => array(
                    'first_name' => $data->anggota->nama_anggota,
                    'email' => $data->email,
                ),
                'expiry' => array(
                    'start_time' => date("Y-m-d H:i:s O"), // waktu sekarang + zona waktu server
                    'unit' => 'minute', // minute / hour / day
                    'duration' => $durationInMinutes,
                )
            );

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Simpan ke PendaftarExtModel
            $pendaftar = PendaftarRakernasModel::create([
                'id_rk' => $id_rk,
                'id_user' => $id_user ?? '-',
                'biaya' => $biaya ?? '-',
                'snap' => $snapToken ?? '-',
                'pengurus' => $kepengurusan ?? '-',
                'ukuran_baju' => $ukuran_baju ?? '-',
                'status' => 'pending',
                'order_id' => $order_id,
            ]);

            $id_prk_baru = $pendaftar->id_prk;

            $urlPembayaran = url('/');

            $message = "Halo *{$data->anggota->nama_anggota}* 👋,

Terima kasih telah mendaftar Rakernas ADAKSI *" . $rakernas->tema . "*.

Silakan lakukan pembayaran pada menu Rakernas melalui tautan berikut:
{$urlPembayaran}

Total tagihan Anda: *Rp " . number_format($biaya, 0, ',', '.') . "*

Mari bersama berjuang untuk Indonesia Emas! 🇮🇩✨

_Salam,_  
*Tim ADAKSI*";

            // Kirim pesan ke user
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
                    'target' => $data->no_hp, // pastikan format nomor sudah internasional (62xxxxx)
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

            return redirect()->route('anggota.bayar', ['snapToken' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Error Pendaftaran Rakernas: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat pendaftaran: ' . $e->getMessage());
        }
    }


    public function validasiBySnapRakernas($snapToken)
    {

        try {
            DB::beginTransaction();
            $ValidDaftar = PendaftarRakernasModel::with([
                'anggota',      // relasi ke tabel anggota
                'user'          // relasi ke tabel user
            ])
                ->where('snap', $snapToken)
                ->firstOrFail();


            // Buat nama file: idcard_random6.png
            $safeIdCard = preg_replace('/[^A-Za-z0-9]/', '', $ValidDaftar->anggota->id_card);
            $randomString = Str::upper(Str::random(6));
            $fileNameOnly = "{$safeIdCard}_{$randomString}";
            $fileName = "{$fileNameOnly}.png";

            // Isi QR adalah id_card + random
            $qrContent = $fileName;

            // Generate QR
            generateGoQrAndSave($qrContent, $fileName);

            // Update status dan simpan nama file
            $ValidDaftar->status = 'valid';
            $ValidDaftar->qrcode = $fileName;
            // $link = 'https://www.adaksi.org/uploads/qrcode/0011900008_22U8U1.png';

            $id_rk = $ValidDaftar->id_rk;

            $sertifikat = SertifikatModel::where('id_wb', $id_rk)->firstOrFail();

            $no_surat = $sertifikat->no_surat;

            // Cari pendaftar dengan no_sertifikat yang LIKE no_surat yang sedang diproses
            $lastDaftar = PendaftarRakernasModel::where('no_sertifikat', 'like', $no_surat . '%')
                ->orderBy('no_urut', 'desc')
                ->first();

            // Jika ditemukan, no_urut = terakhir + 1
            // Jika tidak ditemukan (nomor baru), no_urut = 1
            $no_urut = $lastDaftar ? $lastDaftar->no_urut + 1 : 1;

            $bulan_romawi = [
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII',
            ];

            $bulan = date('n');
            $tahun = date('Y');
            $bulan_rom = $bulan_romawi[$bulan];

            $hasil = $bulan_rom . '/' . $tahun;

            //14-(i)/I/SERT/DPP-ADAKSI/VI/2025

            $no_sertifikat = $sertifikat->no_surat . '-(' . $no_urut . ')/' . $sertifikat->angkatan . '/SERT' . '/' . $sertifikat->unit . '-ADAKSI/' . $hasil;

            $ValidDaftar->no_urut = $no_urut;
            $ValidDaftar->no_sertifikat = $no_sertifikat;

            $ValidDaftar->save();

            DB::commit();

            if ($ValidDaftar->status === 'valid') {
                $message = "Halo " . $ValidDaftar->anggota->nama_anggota . " 👋,

Selamat pendaftaran Anda telah *TERVERIFIKASI* dan *VALID*.

Download *QRCode* melalui link berikut:
https://www.adaksi.org/uploads/qrcode/" . $fileName . "

Simpan baik-baik *QRCode* tersebut untuk akses masuk kegiatan Rakernas nanti.
Anda juga dapat melihat *QRCode* tersebut di akun Anda tepatnya di menu *Rakernas*.
        
📝 Login melalui link berikut ya:
" . config('app.url') . "
        
Terima kasih telah berpartisipasi di Rakernas ADAKSI. Mari bersama berjuang untuk Indonesia Emas! 🇮🇩✨.

Salam,  
*Sistem ADAKSI*";

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
                        'target' => $ValidDaftar->user->no_hp, // pastikan format nomor sudah internasional (62xxxxx)
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
                    \Log::error('WhatsApp API Error: ' . $error_msg);
                }
                curl_close($curl);
            }

            return redirect('/anggota/rakernas')->with('success', 'Anda telah terdaftar!');
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Gagal validasi: ' . $e->getMessage());
            return back()->with('error', 'Gagal validasi: ' . $e->getMessage());
        }
    }

    public function validasi(Request $request, $id)
    {

        $idCardTersedia = NoAnggotaModel::orderBy('id_card', 'asc')->first();

        if ($idCardTersedia) {
            // Gunakan id_card dari no_anggota dan ambil 6 digit terakhir untuk no_urut
            $id_card = $idCardTersedia->id_card;
            $no_urut = (int)substr($id_card, -6);

            // Hapus data id_card dari tabel no_anggota setelah digunakan
            $idCardTersedia->delete();
        } else {
            // Jika tidak ada lagi id_card tersisa di tabel no_anggota
            $anggota_urut = AnggotaModel::orderBy('id_card', 'desc')->first();
            $last_id_card = $anggota_urut->id_card;
            $last_no = (int)substr($last_id_card, -5);
            $no_urut = $last_no + 1;

            // Pastikan nomor urut selalu 5 digit dengan nol di depan menggunakan str_pad()
            // Ini menggantikan seluruh blok if/elseif sebelumnya
            $id_card_padded = str_pad($no_urut, 5, '0', STR_PAD_LEFT);
            $id_card = '00119' . $id_card_padded;
        }

        // Simpan ke data anggota
        $anggota = AnggotaModel::findOrFail($id);
        $anggota->status_anggota = $request->status_anggota;
        $anggota->no_urut = $no_urut;
        $anggota->id_card = $id_card;
        $anggota->save();

        $anggota_user = User::findOrFail($anggota->id_user);
        $iduser = $anggota->id_user;



        // Pesan untuk dikirim
        if ($request->status_anggota === 'aktif') {
            $message = "Halo " . $anggota->nama_anggota . " 👋,

Selamat menjadi bagian dari ADAKSI! Akun Anda di sistem telah berhasil diaktifkan oleh admin. Berikut adalah detail akun Anda:
        
🔑 *Username:* " . $anggota_user->email . "
🔒 *Password:* " . $anggota_user->password_temporary . "
        
Demi keamanan akun, sangat disarankan untuk segera *mengganti password* Anda setelah login pertama.
        
📝 Silakan login melalui link berikut:
base_url: " . config('app.url') . "
Silakan login menggunakan akun Anda.
        
✌️Serta upload pas photo untuk download KTA di akun Anda.

Terima kasih telah bergabung bersama ADAKSI. Mari bersama-sama berjuang untuk Indonesia Emas! 🇮🇩✨";
        } else {
            $message = "Halo " . $anggota->nama_anggota . " 👋,
Maaf, Bukti transfer *Tidak Valid*, silakan lakukan pendaftaran ulang dengan melampirkan bukti yang *Valid*.
        
Terima kasih atas kerjasamanya.
        
Salam,  
*Sistem ADAKSI*";

            $anggota_maudel = AnggotaModel::where('id_user', $iduser)->firstOrFail();

            // Hapus file bukti_tf_pendaftaran jika ada
            if ($anggota_maudel->bukti_tf_pendaftaran) {
                $filePath = public_path('uploads/bukti_tf_pendaftaran/' . $anggota_maudel->bukti_tf_pendaftaran);
                if (File::exists($filePath) && is_file($filePath)) {
                    File::delete($filePath);
                }
            }

            User::where('id_user', $anggota->id_user)->delete();
        }

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
            // Jika error, log atau tampilkan
            \Log::error('WhatsApp API Error: ' . $error_msg);
        }
        curl_close($curl);


        // mengubah password temporary menjadi null di tabel user
        $anggota_user->password_temporary = null;
        $anggota_user->save();

        // notify()->success('Anggota berhasil divalidasi dan notifikasi telah dikirim ke semua admin.');
        return redirect('/admin/calonanggota')->with('Validasi anggota berhasil dilakukan!');
        //return redirect()->back();
    }
}
