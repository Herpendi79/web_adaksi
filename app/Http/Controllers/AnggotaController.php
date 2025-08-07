<?php

namespace App\Http\Controllers;

// php spreetsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Mail\AnggotaValidateMail;
use App\Models\AnggotaModel;
use App\Models\WebinarModel;
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


class AnggotaController extends Controller
{
    protected $defaultPassword;
    public function __construct()
    {

        $password = str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $pass = substr($password, 0, 8);
        $this->defaultPassword = $pass;
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

            'bukti_transfer.required' => 'Bukti transfer wajib diunggah.',
            'bukti_transfer.file' => 'Bukti transfer harus berupa file.',
            'bukti_transfer.mimes' => 'Bukti transfer harus berupa file JPG, JPEG, PNG, atau PDF.',
            'bukti_transfer.max' => 'Ukuran bukti transfer maksimal 2MB.',

            'status_anggota.in' => 'Status anggota harus salah satu dari pending, aktif, atau nonaktif.',

            'foto.file' => 'Foto harus berupa file.',
            'foto.mimes' => 'Foto harus berupa file JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
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
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status_anggota' => 'nullable|in:pending,aktif,nonaktif',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ], $messages);
        // die;

        try {
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($this->defaultPassword),
                'password_temporary' => $this->defaultPassword,
                'role' => 'anggota',
                'no_hp' => $request->no_hp,
            ]);
            AnggotaModel::create([
                'id_user' => $user->id_user,
                'nama_anggota' => $request->nama_anggota,
                'nip_nipppk' => $request->nip_nipppk,
                'status_dosen' => $request->status_dosen,
                'homebase_pt' => $request->homebase_pt,
                'provinsi' => $request->provinsi,
                'status_anggota' => 'pending',
                'bukti_tf_pendaftaran' => $this->uploadFile($request->file('bukti_transfer'), 'uploads/bukti_tf_pendaftaran'),
            ]);


            $message = "🔔 *Pemberitahuan Pendaftaran Anggota Baru ADAKSI* 🔔

Halo Admin 👋,
            
Telah terdaftar anggota calon baru di sistem ADAKSI yang menunggu proses validasi akun. Berikut detailnya:
            
👤 *Nama:* " . $request->nama_anggota . "
📧 *Email:* " . $request->email . "
📱 *No. HP:* " . $request->no_hp . "
🏢 *Homebase PT:* " . $request->homebase_pt . "
🌍 *Provinsi:* " . $request->provinsi . "
🎓 *Status Dosen:* " . $request->status_dosen . "
🆔 *NIP/NIPPPK:* " . $request->nip_nipppk . "
            
📌 *Status Anggota:* Pending
        
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
                    \Log::error('WhatsApp API Error: ' . $error_msg);
                }
                curl_close($curl);
            }

            // Kirim pesan ke user
            //foreach ($users as $user) {
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
                    'message' => "Halo " . $request->nama_anggota . ",\n\n" .
                        "Terima kasih telah mendaftar sebagai anggota ADAKSI. Akun Anda sedang dalam *proses validasi* oleh admin. Silakan tunggu dalam waktu maksimal 2x24 jam.\n\n" .
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
                \Log::error('WhatsApp API Error: ' . $error_msg);
            }
            curl_close($curl);
            // }

            return redirect()->back()->with('success', 'Pendaftaran calon anggota berhasil, periksa WA anda untuk update selanjutnya. Salam ADAKSI!');
        } catch (\Exception $e) {
            notify()->error('Terjadi kesalahan saat pendaftaran anggota: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
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
                        ->orWhere('email', 'like', '%' . $request->search . '%');
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

        $data = [
            'foto' => $anggota->foto ?? 'foto.jpg', // ganti sesuai nama file foto
            'nama_anggota' => $anggota->nama_anggota,
            'nip_nipppk' => $anggota->nip_nipppk,
            'id_card' => $anggota->id_card,
            'created_at' => $anggota->created_at->format('d-m-Y'),
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

        $webinar = WebinarModel::with('fasilitas')
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
                'status'
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
        $destination = base_path('../public_html/' . $path); // sesuaikan jika Laravel di luar public_html
        $destination = base_path('public/' . $path); // sesuaikan jika Laravel di luar public_html

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
                'keterangan' => $keterangan,
                'bukti_tf' => $bukti_tf_path,
                'token' => null,
                'biaya' => $biaya ?? 0,
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
                    \Log::error('WhatsApp API Error: ' . $error_msg);
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
                \Log::error('WhatsApp API Error: ' . $error_msg);
            }
            curl_close($curl);

            return redirect()->back()->with('success', 'Pendaftaran kegiatan berhasil, periksa WA anda untuk update selanjutnya. Salam ADAKSI!');
        } catch (\Exception $e) {
            \Log::error('Error Pendaftaran Webinar: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat pendaftaran: ' . $e->getMessage());
        }
    }

    public function showAllRakernas(Request $request)
    {
        $rakernas = RakernasModel::when($request->search, function ($query) use ($request) {
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
            ->orderBy('tanggal_mulai', 'ASC')
            ->paginate(10);

        $rakernas->getCollection()->transform(function ($item) {
            $kode_unik = rand(100, 999);
            $item->biaya_unik = ($item->biaya ?? 0) + $kode_unik;
            return $item;
        });


        $id_user = Auth::id();

        // Ambil semua id_rk yang sudah didaftarkan user ini
        $rakernas_terdaftar_ids = PendaftarRakernasModel::where('id_user', $id_user)
            ->pluck('id_rk')
            ->toArray();

        return view('anggota_page.rakernas.index', compact('rakernas', 'rakernas_terdaftar_ids'));
    }

    public function store_registrasi_rakernas(Request $request)
    {
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

        try {
            $id_rk = $request->id_rk;
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
            $rakernas = RakernasModel::findOrFail($id_rk);

            // Upload file bukti transfer
            $bukti_tf_path = null;
            if ($request->hasFile('bukti_transfer')) {
                $bukti_tf_path = $this->uploadFilePendaftar(
                    $request->file('bukti_transfer'),
                    'uploads/bukti_tf_pendaftaran'
                );
            }

            // Simpan ke PendaftarExtModel
            PendaftarRakernasModel::create([
                'id_rk' => $id_rk,
                'id_user' => $id_user ?? '-',
                'bukti_tf' => $bukti_tf_path,
                'keterangan' => $keterangan ?? '-',
                'status' => 'pending',
            ]);

            $message = "🔔 *Pemberitahuan Pendaftaran Rakernas* 🔔

Halo Admin 👋,
            
Telah terdaftar peserta baru Rakernas di sistem ADAKSI yang menunggu proses validasi. Berikut detailnya:
            
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
                    \Log::error('WhatsApp API Error: ' . $error_msg);
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
                        "Terima kasih telah mendaftar *Rakernas* dengan tema *" . $rakernas->tema . "*" .
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
                \Log::error('WhatsApp API Error: ' . $error_msg);
            }
            curl_close($curl);

            return redirect()->back()->with('success', 'Pendaftaran rakernas berhasil, periksa WA anda untuk update selanjutnya. Salam ADAKSI!');
        } catch (\Exception $e) {
            \Log::error('Error Pendaftaran Rakernas: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat pendaftaran: ' . $e->getMessage());
        }
    }
}
