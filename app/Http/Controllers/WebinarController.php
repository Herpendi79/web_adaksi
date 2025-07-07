<?php

namespace App\Http\Controllers;

// php spreetsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Mail\AnggotaValidateMail;
use App\Models\WebinarModel;
use App\Models\FasilitasModel;
use App\Models\PendaftarExtModel;
use App\Models\AnggotaModel;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;



class WebinarController extends Controller
{

    public function index()
    {
        $webinars = WebinarModel::where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $latestWebinars = WebinarModel::orderBy('tanggal_mulai', 'desc')->limit(7)->get();


        return view('webinar', compact('webinars', 'latestWebinars',));
    }

    public function agenda($id)
    {
        $agenda = WebinarModel::with('fasilitas')
            ->where('id_wb', $id)
            ->firstOrFail();

        $latestWebinars = WebinarModel::orderBy('tanggal_mulai', 'desc')->limit(7)->get();

        return view('agenda', compact('agenda', 'latestWebinars'));
    }

    public function registrasi($id)
    {
        // Ambil data webinar
        $registrasi = WebinarModel::findOrFail($id);

        // Generate angka acak 3 digit untuk kode unik (misal: 123)
        $kode_unik = rand(100, 999);
        $id_wb = $registrasi->id_wb;

        // Hitung total pembayaran
        $total_bayar = $registrasi->biaya_non_anggota + $kode_unik;

        // Kirim ke view
        return view('registrasi', compact('registrasi', 'kode_unik', 'total_bayar', 'id_wb'));
    }

    public function daftar()
    {
        // Ambil data webinar


        // Generate angka acak 3 digit untuk kode unik (misal: 123)
        $kode_unik = rand(100, 999);

        // Kirim ke view
        return view('guest_page.anggota_daftar_form', compact('kode_unik'));
    }



    public function showAllWebinar(Request $request)
    {
        $webinar = WebinarModel::with('fasilitas') // <- tambahkan eager loading di sini
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('judul', 'like', '%' . $request->search . '%')
                        ->orWhere('tanggal_mulai', 'like', '%' . $request->search . '%')
                        ->orWhere('moderator', 'like', '%' . $request->search . '%')
                        ->orWhere('hari', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);

        $pendaftarBelumTokenPerWebinar = PendaftarExtModel::selectRaw('id_wb, COUNT(*) as jumlah')
            ->where(function ($query) {
                $query->whereNull('token')
                    ->orWhere('token', '');
            })
            ->groupBy('id_wb')
            ->pluck('jumlah', 'id_wb');

        $valid = PendaftarExtModel::selectRaw('id_wb, COUNT(*) as jumlah')
            ->whereNotNull('token')
            ->where('token', '!=', '')
            ->groupBy('id_wb')
            ->pluck('jumlah', 'id_wb');

        $total_biaya = PendaftarExtModel::selectRaw('id_wb, SUM(biaya) as total')
            ->whereNotNull('token')
            ->where('token', '!=', '')
            ->groupBy('id_wb')
            ->pluck('total', 'id_wb');

        // $total_biaya = $total->get('id_wb', 0);


        return view('admin_page.webinar.index', compact('webinar', 'pendaftarBelumTokenPerWebinar', 'valid', 'total_biaya'));
    }

    public function pendaftar(Request $request, $id)
    {
        $webinar = PendaftarExtModel::where('id_wb', $id)
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('no_hp', 'like', '%' . $request->search . '%')
                        ->orWhere('nip', 'like', '%' . $request->search . '%')
                        ->orWhere('home_base', 'like', '%' . $request->search . '%')
                        ->orWhere('provinsi', 'like', '%' . $request->search . '%');
                });
            })
            ->orderByRaw('CASE WHEN token IS NULL OR token = "" THEN 0 ELSE 1 END') // Prioritaskan NULL/"" di atas
            ->orderBy('created_at', 'desc') // urutan tambahan jika sama-sama null
            ->paginate(10);
             $data = $webinar->first();

        $pendaftar = WebinarModel::where('id_wb', $id)->firstOrFail();
        $jumlah_valid = PendaftarExtModel::where('id_wb', $id)
            ->whereNotNull('token')
            ->count();

         $total_masuk = PendaftarExtModel::selectRaw('id_wb, SUM(biaya) as total')
            ->whereNotNull('token')
            ->where('token', '!=', '')
            ->groupBy('id_wb')
            ->pluck('total', 'id_wb');


        $total = PendaftarExtModel::where('id_wb', $id)
            ->whereNotNull('token')
            ->sum('biaya');


        return view('admin_page.webinar.pendaftar', compact('webinar', 'id', 'pendaftar', 'total_masuk', 'data'));
    }

    public function create()
    {
        return view('admin_page.webinar.create'); // atau view yang kamu gunakan
    }

    public function store(Request $request)
    {
        $messages = [
            'judul.required' => 'Judul wajib diisi.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'hari.required' => 'Hari wajib diisi.',
            'hari.string' => 'Hari harus berupa teks.',
            'hari.max' => 'Hari maksimal 60 karakter.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'pukul.required' => 'Pukul wajib diisi.',
            'link_zoom.required.url' => 'Link Zoom wajib diisi & berupa link.',
            'bayar_free.required' => 'Kepesertaan wajib dipilih.',
            'moderator.required' => 'Moderator wajib diisi.',
            'flyer.required' => 'Flyer wajib diunggah.',
            'flyer.mimes' => 'Flyer harus berupa JPG, JPEG, PNG, atau PDF.',
            'flyer.max' => 'Ukuran Flyer maksimal 2MB.',
            'sertifikat_depan.mimes' => 'Harus berupa JPG, JPEG, PNG',
            'sertifikat_belakang.mimes' => 'Harus berupa JPG, JPEG, PNG',
            'fasilitas.*.nama.required' => 'Nama fasilitas wajib diisi.',
            'fasilitas.*.link.url' => 'Harus berupa link',
        ];

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:2000',
            'hari' => 'required|string|max:60',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'pukul' => 'required|string',
            'link_zoom' => 'required|string|url',
            'bayar_free' => 'required|string',
            'moderator' => 'required|string',
            'flyer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'sertifikat_depan' => 'required|file|mimes:pdf|max:2048',
            'sertifikat_belakang' => 'required|file|mimes:pdf|max:2048',
            'fasilitas' => 'nullable|array',
            'fasilitas.*.nama' => 'required|string|max:255',
            'fasilitas.*.link' => 'required|url',
        ], $messages);

        try {
            DB::beginTransaction();

            $webinar = WebinarModel::create([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'hari' => $request->hari,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'pukul' => $request->pukul,
                'link_zoom' => $request->link_zoom,
                'bayar_free' => $request->bayar_free,
                'biaya_anggota_aktif' => $request->biaya_anggota_aktif,
                'biaya_anggota_non_aktif' => $request->biaya_anggota_non_aktif,
                'biaya_non_anggota' => $request->biaya_non_anggota,
                'moderator' => $request->moderator,
                'flyer' => $this->uploadFile($request->file('flyer'), 'uploads/webinar'),
                'sertifikat_depan' => $this->uploadFile($request->file('sertifikat_depan'), 'uploads/webinar'),
                'sertifikat_belakang' => $this->uploadFile($request->file('sertifikat_belakang'), 'uploads/webinar'),
            ]);

            // Simpan data fasilitas jika ada
            if ($request->has('fasilitas')) {
                foreach ($request->fasilitas as $item) {
                    FasilitasModel::create([
                        'id_wb' => $webinar->id_wb,
                        'nama' => $item['nama'] ?? null,
                        'link' => $item['link'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('webinar.index')->with('success', 'Webinar berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            notify()->error('Gagal menyimpan data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $webinar = WebinarModel::with('fasilitas')
            ->where('id_wb', $id)
            ->firstOrFail();

        return view('admin_page.webinar.edit', compact('webinar'));
    }

    public function ubah($id)
    {
        $webinar = WebinarModel::with('fasilitas')
            ->where('webinar.id_wb', $id)
            ->join('fasilitas', 'webinar.id_wb', '=', 'fasilitas.id_wb')
            ->firstOrFail();
        // pastikan anggota yang diambil adalah anggota yang dimiliki oleh user yang sedang login
        return view('admin_page.webinar.ubah', compact('webinar'));
    }

    public function uploadFile($file, $path, $prefix = '')
    {
        $filename = $prefix . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Ganti dengan path absolut sesuai lokasi sebenarnya
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

    public function uploadFilePendaftar($file, $path, $prefix = '')
    {
        $filename = $prefix . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Ganti dengan path absolut sesuai lokasi sebenarnya
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

    public function update(Request $request, $id)
    {

        $messages = [
            'judul.required' => 'Judul wajib diisi.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'hari.required' => 'Hari wajib diisi.',
            'hari.string' => 'Hari harus berupa teks.',
            'hari.max' => 'Hari maksimal 60 karakter.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'pukul.required' => 'Pukul wajib diisi.',
            'link_zoom.required.url' => 'Link Zoom wajib diisi & berupa link.',
            'bayar_free.required' => 'Kepesertaan wajib dipilih.',
            'moderator.required' => 'Moderator wajib diisi.',
            'sertifikat_depan.mimes' => 'Harus file PDF.',
            'sertifikat_belakang.mimes' => 'Harus file PDF.',
            'fasilitas.*.nama.required' => 'Nama fasilitas wajib diisi.',
            'fasilitas.*.link.url' => 'Harus berupa link',
        ];

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:2000',
            'hari' => 'required|string|max:60',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'pukul' => 'required|string',
            'link_zoom' => 'required|string|url',
            'bayar_free' => 'required|string',
            'moderator' => 'required|string',
            'flyer' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'sertifikat_depan' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'sertifikat_belakang' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'fasilitas' => 'nullable|array',
            'fasilitas.*.nama' => 'required|string|max:255',
            'fasilitas.*.link' => 'nullable|url',
        ], $messages);

        try {
            $webinar = WebinarModel::findOrFail($id);

            $webinar->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'hari' => $request->hari,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'pukul' => $request->pukul,
                'link_zoom' => $request->link_zoom,
                'bayar_free' => $request->bayar_free,
                'biaya_anggota_aktif' => $request->biaya_anggota_aktif,
                'biaya_anggota_non_aktif' => $request->biaya_anggota_non_aktif,
                'biaya_non_anggota' => $request->biaya_non_anggota,
                'moderator' => $request->moderator,
            ]);

            if ($request->hasFile('flyer')) {
                $webinar->flyer = $this->uploadFile($request->file('flyer'), 'uploads/webinar', 'flyer_');
            }
            if ($request->hasFile('sertifikat_depan')) {
                $webinar->sertifikat_depan = $this->uploadFile($request->file('sertifikat_depan'), 'uploads/webinar', 'depan_');
            }
            if ($request->hasFile('sertifikat_belakang')) {
                $webinar->sertifikat_belakang = $this->uploadFile($request->file('sertifikat_belakang'), 'uploads/webinar', 'belakang_');
            }

            $webinar->save();

            // Ambil ID fasilitas lama dari DB
            $existingFasilitasIds = FasilitasModel::where('id_wb', $webinar->id_wb)->pluck('id_fas')->toArray();

            // ID yang dikirim user dari form
            $requestFasilitasIds = [];

            // Proses input fasilitas
            foreach ($request->fasilitas as $item) {
                if (!empty($item['id_fas'])) {
                    $requestFasilitasIds[] = $item['id_fas']; // Simpan ID yang dikirim user

                    FasilitasModel::where('id_fas', $item['id_fas'])->update([
                        'nama' => $item['nama'] ?? '',
                        'link' => $item['link'] ?? '',
                    ]);
                } else {
                    FasilitasModel::create([
                        'id_wb' => $webinar->id_wb,
                        'nama' => $item['nama'] ?? '',
                        'link' => $item['link'] ?? '',
                    ]);
                }
            }

            $toDelete = array_diff($existingFasilitasIds, $requestFasilitasIds);
            if (!empty($toDelete)) {
                FasilitasModel::whereIn('id_fas', $toDelete)->delete();
            }

            return redirect()->route('admin_page.webinar.index')->with('success', 'Webinar berhasil diperbarui.');
        } catch (\Exception $e) {
            notify()->error('Gagal menyimpan data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function publish($id)
    {
        try {
            // Temukan data webinar
            $webinar = WebinarModel::findOrFail($id);

            // Ubah status menjadi 'publish'
            $webinar->status = 'publish';
            $webinar->save();

            return redirect()->back()->with('success', 'Webinar berhasil dipublish.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mempublish webinar: ' . $e->getMessage());
        }
    }

    public function selesai($id)
    {
        try {
            // Temukan data webinar
            $webinar = WebinarModel::findOrFail($id);

            // Ubah status menjadi 'publish'
            $webinar->status = 'selesai';
            $webinar->save();

            return redirect()->back()->with('success', 'Webinar berhasil dipublish.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mempublish webinar: ' . $e->getMessage());
        }
    }

    public function validasiPendaftar(Request $request, $id)
    {
        try {

            $token = Str::random(8);

            $ValidDaftar = PendaftarExtModel::where('id_pwe', $id)
                ->whereNull('token') // hanya validasi jika belum tervalidasi
                ->firstOrFail();

            $email = $ValidDaftar->email;
            $no_hp = $ValidDaftar->no_hp;

            $cekAnggota = User::where(function ($query) use ($email, $no_hp) {
                $query->where('email', $email)
                    ->orWhere('no_hp', $no_hp);
            })
                ->first();

            $id_wb = $ValidDaftar->id_wb;

            $webinars = WebinarModel::find($id_wb);

            if ($request->valid === 'valid') {
                $ValidDaftar->token = $token;
                $ValidDaftar->save();

                if ($cekAnggota) {
                    $message = "Halo " . $ValidDaftar->nama . " 👋,

Selamat pendaftaran Anda telah diverifikasi oleh admin dan *valid*. 
Setelah kegiatan *" . $webinars->judul . "* selesai anda dapat download sertifikat dan fasilitas lainnya di akun anda masing-masing, 
tepatnya di menu *Webinar-> Klik Download Fasilitas*.
        
📝 Login melalui link berikut ya:
" . config('app.url') . "
        
Terima kasih telah berpartisipasi bersama ADAKSI. Mari bersama berjuang untuk Indonesia Emas! 🇮🇩✨.

Salam,  
*Sistem ADAKSI*";
                } else {
                    $message = "Halo " . $ValidDaftar->nama . " 👋,

Selamat pendaftaran Anda telah diverifikasi oleh admin dan *valid*. 
Setelah kegiatan *" . $webinars->judul . "* selesai anda dapat download sertifikat dan fasilitas lainnya dengan akses berikut:
        
🔑 *Username:* " . $ValidDaftar->email . "
🔒 *Token:* " . $token . "
        
Simpan dengan baik pesan ini ✌️
        
📝 Login melalui link berikut ya:
" . config('app.url') . "
        
Terima kasih telah berpartisipasi bersama ADAKSI. Mari bersama berjuang untuk Indonesia Emas! 🇮🇩✨.

Salam,  
*Sistem ADAKSI*";
                }
            } else {
                $message = "Halo " . $ValidDaftar->nama . " 👋,

Mohon maaf pendaftaran anda di kegiatan *" . $webinars->judul . "* dinyatakan *ditolak* dengan keterangan berikut:
*" . $request->keterangan . "*.

Silakukan melakukan pendaftaran ulang dengan memperhatikan keterangan diatas.   
Terima kasih telah berpartisipasi bersama ADAKSI. Mari bersama berjuang untuk Indonesia Emas! 🇮🇩✨.

Salam,  
*Sistem ADAKSI*";

                PendaftarExtModel::where('id_pwe', $ValidDaftar->id_pwe)->delete();
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
                    'target' => $ValidDaftar->no_hp, // pastikan format nomor sudah internasional (62xxxxx)
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
            return redirect()->back()->with('success', 'Berhasil Divalidasi');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Berhasil Divalidasi', $e->getMessage());
        }
    }



    public function store_registrasi(Request $request)
    {
        $token = Str::random(8);

        $messages = [
            'nama_anggota.required' => 'Nama wajib diisi.',
            'nama_anggota.string' => 'Nama harus berupa teks.',
            'nama_anggota.max' => 'Nama maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',

            'nip_nipppk.required' => 'NIP/NIPPPK wajib diisi.',
            'nip_nipppk.string' => 'NIP/NIPPPK harus berupa teks.',
            'nip_nipppk.max' => 'NIP/NIPPPK maksimal 50 karakter.',

            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.string' => 'Nomor HP harus berupa teks.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',

            'status_dosen.required' => 'Status dosen wajib diisi.',
            'status_dosen.string' => 'Status dosen harus berupa teks.',

            'homebase_pt.required' => 'Homebase PT / Instansi wajib diisi.',
            'homebase_pt.string' => 'Homebase PT / Instansi harus berupa teks.',
            'homebase_pt.max' => 'Homebase PT / Instansi maksimal 100 karakter.',

            'provinsi.required' => 'Provinsi wajib diisi.',
            'provinsi.string' => 'Provinsi harus berupa teks.',
            'provinsi.max' => 'Provinsi maksimal 100 karakter.',

            'keterangan.required' => 'Wajib diisi.',
            'keterangan.string' => 'Harus berupa teks.',
            'keterangan.max' => 'Maksimal 100 karakter.',

            'bukti_transfer.required' => 'Bukti transfer wajib diunggah.',
            'bukti_transfer.file' => 'Bukti transfer harus berupa file.',
            'bukti_transfer.mimes' => 'Bukti transfer harus berupa file JPG, JPEG, PNG, atau PDF.',
            'bukti_transfer.max' => 'Ukuran bukti transfer maksimal 2MB.',

            'foto.file' => 'Foto harus berupa file.',
            'foto.mimes' => 'Foto harus berupa file JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ];

        $email = $request->email;
        $existingEmail = PendaftarExtModel::where('email', $email)->where('id_wb', '=', $request->id_wb)->first();
        if ($existingEmail) {
            notify()->error('Email sudah terdaftar di Kegiatan ini. Silakan gunakan Email yang berbeda.');
            return redirect()->back()->withInput();
        }
        $no_hp = $request->no_hp;
        $existingWA = PendaftarExtModel::where('no_hp', $no_hp)->where('id_wb', '=', $request->id_wb)->first();
        if ($existingWA) {
            notify()->error('No HP/WA sudah terdaftar di Kegiatan ini. Silakan gunakan No HP/WA yang berbeda.');
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
            'keterangan' => 'required|string|max:255',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ], $messages);
        // die;

        try {

            $webinar = WebinarModel::findOrFail($request->id_wb);

            PendaftarExtModel::create([
                'id_wb' => $request->id_wb,
                'nama' => $request->nama_anggota,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'nip' => $request->nip_nipppk,
                'status' => $request->status_dosen,
                'home_base' => $request->homebase_pt,
                'provinsi' => $request->provinsi,
                'keterangan' => $request->keterangan,
                'bukti_tf' => $this->uploadFilePendaftar($request->file('bukti_transfer'), 'uploads/bukti_tf_pendaftaran'),
                'token' => null,
                'biaya' => $request->biaya
            ]);

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
                    'target' => $request->no_hp, // pastikan format nomor sudah internasional (62xxxxx)
                    'message' => "Halo " . $request->nama_anggota . ",\n\n" .
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
            notify()->error('Terjadi kesalahan saat pendaftaran: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function sertifikat($id){

        $webinar = WebinarModel::with('pendaftar')->findOrFail($id);

        return view('components.sertifikat', compact('webinar'));

    }

    public function download_sertifikat(){

        //$webinar = WebinarModel::with('pendaftar')->findOrFail($id);

        return view('download', compact('webinar'));

    }

     public function showFasilitasAkses(){

        //$webinar = WebinarModel::with('pendaftar')->findOrFail($id);

        return view('download', compact('webinar'));

    }
}
