<?php

namespace App\Http\Controllers;

use App\Models\AbsensiRakernasModel;
use Illuminate\Http\Request;
use App\Models\RakernasModel;
use App\Models\SertifikatModel;
use App\Models\PendaftarRakernasModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RakernasController extends Controller
{
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
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);


        $rakernas = RakernasModel::withCount(['pendaftar as pending_pendaftar_count' => function ($query) {
            $query->where('status', 'valid');
        }])
            ->paginate(10);

        $total_biaya = $rakernas->mapWithKeys(function ($item) {
            return [
                $item->id_rk => $item->pending_pendaftar_count * $item->biaya
            ];
        });


        $valid = PendaftarRakernasModel::selectRaw('id_rk, COUNT(*) as jumlah')
            ->where('status', '=', 'valid')
            ->groupBy('id_rk')
            ->pluck('jumlah', 'id_rk');

        $pending = PendaftarRakernasModel::selectRaw('id_rk, COUNT(*) as jumlah')
            ->where('status', '=', 'pending')
            ->groupBy('id_rk')
            ->pluck('jumlah', 'id_rk');

        $jumlahPendaftarPerWebinar = PendaftarRakernasModel::selectRaw('id_rk, COUNT(*) as jumlah')
            ->groupBy('id_rk')
            ->pluck('jumlah', 'id_rk');


        return view('admin_page.rakernas.index', compact('rakernas', 'total_biaya', 'valid', 'pending', 'jumlahPendaftarPerWebinar'));
    }

    public function pendaftar(Request $request, $id)
    {
        // Ambil data pendaftar rakernas
        $pendaftar_rakernas = PendaftarRakernasModel::where('id_rk', $id)
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('id_card', 'like', '%' . $request->search . '%')
                        ->orWhereHas('anggota', function ($qa) use ($request) {
                            $qa->where('nama_anggota', 'like', '%' . $request->search . '%')
                                ->orWhere('homebase_pt', 'like', '%' . $request->search . '%')
                                ->orWhere('provinsi', 'like', '%' . $request->search . '%');
                        });
                });
            })
            ->with('anggota')
            ->orderBy('status', 'asc')
            ->paginate(10);

        $data = $pendaftar_rakernas->first();

        // Ambil data rakernas untuk menghitung total biaya pending
        $rakernas = RakernasModel::withCount(['pendaftar as pending_pendaftar_count' => function ($query) {
            $query->where('status', 'valid');
        }])
            ->paginate(10);

        $total_masuk = $rakernas->mapWithKeys(function ($item) {
            return [
                $item->id_rk => $item->pending_pendaftar_count * $item->biaya
            ];
        });

        return view('admin_page.rakernas.pendaftar', [
            'pendaftar_rakernas' => $pendaftar_rakernas,
            'rakernas' => $rakernas,
            'id' => $id,
            'total_masuk' => $total_masuk,
            'data' => $data,
        ]);
    }


    public function validasiPendaftar(Request $request, $id)
    {

        try {
            DB::beginTransaction();
            $ValidDaftar = PendaftarRakernasModel::with([
                'anggota',      // relasi ke tabel anggota
                'user'          // relasi ke tabel user
            ])
                ->where('id_prk', $id)
                ->firstOrFail();


            // Buat nama file: idcard_random6.png
            $safeIdCard = preg_replace('/[^A-Za-z0-9]/', '', $ValidDaftar->anggota->id_card);
            $randomString = Str::upper(Str::random(6));
            $fileNameOnly = "{$safeIdCard}_{$randomString}";
            $fileName = "{$fileNameOnly}.png";

            // Isi QR adalah id_card + random
            $qrContent = $fileNameOnly;

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

            if ($request->valid === 'valid') {
                $message = "Halo " . $ValidDaftar->anggota->nama_anggota . " 👋,

Selamat pendaftaran Anda telah diverifikasi oleh admin dan *VALID*.

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
            } else {
                $message = "Halo " . $ValidDaftar->anggota->nama_anggota . " 👋,

Mohon maaf pendaftaran anda di kegiatan *Rakernas* dinyatakan *DITOLAK* dengan keterangan berikut:
*" . $request->keterangan . "*.

Silakukan melakukan pendaftaran ulang dengan memperhatikan keterangan diatas.   
Terima kasih telah berpartisipasi bersama ADAKSI. Mari bersama berjuang untuk Indonesia Emas! 🇮🇩✨.

Salam,  
*Sistem ADAKSI*";

                PendaftarRakernasModel::where('id_prk', $ValidDaftar->id_prk)->delete();
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



            return redirect()->back()->with('success', '!');
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Gagal validasi: ' . $e->getMessage());
            return back()->with('error', 'Gagal validasi: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin_page.rakernas.create');
    }


    public function absensi_create(Request $request)
    {
        return view('admin_page.rakernas.absensi_create');
    }

    public function checkQrCode(Request $request)
    {
        $request->validate([
            'scan' => 'required|string'
        ]);

        $scan = $request->scan;

        $pendaftar = PendaftarRakernasModel::with('anggota')
            ->where('qrcode', $scan)
            ->whereHas('anggota')
            ->latest()
            ->first();

        if ($pendaftar && $pendaftar->anggota) {
            $fotoFile = $pendaftar->anggota->foto;
            $fotoPathServer = public_path('uploads/foto_anggota/' . $fotoFile);

            if (!$fotoFile || !file_exists($fotoPathServer)) {
                // jika kosong atau file tidak ditemukan, gunakan foto.jpg default
                $fotoFile = 'foto.jpg';
            }

            $fotoPath = asset('uploads/foto_anggota/' . $fotoFile);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id_card' => $pendaftar->anggota->id_card,
                    'nama_anggota' => $pendaftar->anggota->nama_anggota,
                    'homebase_pt' => $pendaftar->anggota->homebase_pt,
                    'provinsi' => $pendaftar->anggota->provinsi,
                    'foto' => $fotoPath,
                    'id_prk' => $pendaftar->id_prk, // <-- tambahkan ini
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'QR Code tidak terdaftar atau anggota tidak ditemukan.'
        ], 404);
    }

    public function simpanAbsensi(Request $request)
    {
        $request->validate([
            'id_prk' => 'required|integer|exists:pendaftar_rakernas,id_prk',
        ]);

        $id_prk = $request->id_prk;
        $today = Carbon::now()->toDateString(); // hanya tanggal untuk validasi

        // Cek jika sudah absen pada hari ini
        $sudahAbsen = AbsensiRakernasModel::where('id_prk', $id_prk)
            ->whereDate('kehadiran', $today)
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'status' => 'error',
                'message' => 'Peserta sudah absen sebelumnya pada hari ini.'
            ], 409);
        }

        // Simpan absensi dengan kehadiran = timestamp sekarang
        AbsensiRakernasModel::create([
            'id_prk' => $id_prk,
            'kehadiran' => Carbon::now() // otomatis tersimpan ke kolom timestamp
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi berhasil disimpan.'
        ]);
    }
    public function store_absensi(Request $request)
    {
        return view('admin_page.rakernas.create');
    }

    public function uploadFile($file, $path, $prefix = '')
    {
        $filename = $prefix . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();


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

    public function store(Request $request)
    {
        $messages = [
            'tema.required' => 'Tema wajib diisi.',
            'tema.max' => 'Tema maksimal 255 karakter.',
            'tema.string' => 'Tema wajib diisi.',

            'tempat.required' => 'Tempat wajib diisi.',
            'tempat.string' => 'Tempat harus berupa teks.',
            'tempat.max' => 'Tempat maksimal 255 karakter.',

            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',

            'biaya.required' => 'Biaya wajib diisi.',
            'biaya.numeric' => 'Biaya wajib angka.',

            'fasilitas.required' => 'Fasilitas wajib diisi.',
            'fasilitas.string' => 'Fasilitas harus berupa teks.',
            'fasilitas.max' => 'Fasilitas maksimal 255 karakter.',

            'no_surat.required' => 'Nomor surat wajib diisi.',
            'angkatan.required' => 'Angkatan wajib diisi.',
            'unit.required' => 'Unit wajib diisi.',
            'sertifikat_depan.mimes' => 'Harus berupa JPG, JPEG, PNG',
            'sertifikat_belakang.mimes' => 'Harus berupa JPG, JPEG, PNG',
        ];

        // Sanitize biaya from "Rp 500.000" ➔ "500000"
        $request->merge([
            'biaya' => preg_replace('/[^\d]/', '', $request->biaya)
        ]);

        $request->validate([
            'tema' => 'required|string|max:255',
            'tempat' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'biaya' => 'required|numeric',
            'fasilitas' => 'required|string|max:255',
            'no_surat' => 'required|string',
            'angkatan' => 'required|string',
            'unit' => 'required|string',
            'sertifikat_depan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'sertifikat_belakang' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], $messages);

        try {
            DB::beginTransaction();

            logger()->info('Request data', $request->all());

            $rakernas = RakernasModel::create([
                'tema' => $request->tema,
                'tempat' => $request->tempat,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'biaya' => $request->biaya,
                'fasilitas' => $request->fasilitas,
                'sertifikat_depan' => $this->uploadFile($request->file('sertifikat_depan'), 'uploads/rakernas'),
                'sertifikat_belakang' => $this->uploadFile($request->file('sertifikat_belakang'), 'uploads/rakernas'),
            ]);

            $sertifikat = SertifikatModel::create([
                'id_wb' => $rakernas->id_rk,
                'no_surat' => $request->no_surat,
                'angkatan' => $request->angkatan,
                'unit' => $request->unit
            ]);

            logger()->info('Rakernas created', $rakernas->toArray());


            DB::commit();

            return redirect()->back()->with('success', 'Rakernas berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Gagal menyimpan data: ' . $e->getMessage(), ['trace' => $e->getTrace()]);
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $rakernas = RakernasModel::where('id_rk', $id)
            ->firstOrFail();

        $sertifikat = SertifikatModel::where('id_wb', $rakernas->id_rk)
            ->firstOrFail();

        return view('admin_page.rakernas.edit', compact('rakernas', 'sertifikat'));
    }

    public function absensi(Request $request, $id)
    {
        $absensi_rakernas = AbsensiRakernasModel::query()
            ->whereHas('AbsenPendaftar', function ($q) use ($id) {
                $q->where('id_rk', $id);
            })
            ->when($request->kehadiran, function ($query) use ($request) {
                $query->whereDate('kehadiran', $request->kehadiran);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->whereDate('kehadiran', 'like', '%' . $request->search . '%')
                        ->orWhereHas('AbsenPendaftar.anggota', function ($qa) use ($request) {
                            $qa->where('nama_anggota', 'like', '%' . $request->search . '%')
                                ->orWhere('homebase_pt', 'like', '%' . $request->search . '%')
                                ->orWhere('provinsi', 'like', '%' . $request->search . '%')
                                ->orWhere('id_card', 'like', '%' . $request->search . '%');
                        });
                });
            })
            ->with(['AbsenPendaftar.anggota'])
            ->orderBy('kehadiran', 'asc')
            ->paginate(10);

        $rakernas = RakernasModel::findOrFail($id);

        return view('admin_page.rakernas.absensi', compact('absensi_rakernas', 'rakernas'));
    }


    public function update(Request $request, $id_rk)
    {
        $messages = [
            'tema.required' => 'Tema wajib diisi.',
            'tema.max' => 'Tema maksimal 255 karakter.',
            'tema.string' => 'Tema wajib diisi.',

            'tempat.required' => 'Tempat wajib diisi.',
            'tempat.string' => 'Tempat harus berupa teks.',
            'tempat.max' => 'Tempat maksimal 255 karakter.',

            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',

            'biaya.required' => 'Biaya wajib diisi.',
            'biaya.numeric' => 'Biaya wajib angka.',

            'fasilitas.required' => 'Fasilitas wajib diisi.',
            'fasilitas.string' => 'Fasilitas harus berupa teks.',
            'fasilitas.max' => 'Fasilitas maksimal 255 karakter.',

            'no_surat.required' => 'Nomor Surat wajib diisi.',
            'angkatan.required' => 'Angkatan wajib diisi.',
            'unit.required' => 'Unit wajib diisi.',
            'sertifikat_depan.mimes' => 'Harus file PDF.',
            'sertifikat_belakang.mimes' => 'Harus file PDF.',
        ];

        // Sanitize biaya from "Rp 500.000" ➔ "500000"
        $request->merge([
            'biaya' => preg_replace('/[^\d]/', '', $request->biaya)
        ]);

        $request->validate([
            'tema' => 'required|string|max:255',
            'tempat' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'biaya' => 'required|numeric',
            'fasilitas' => 'required|string|max:255',
            'no_surat' => 'required|string',
            'angkatan' => 'required|string',
            'unit' => 'required|string',
            'sertifikat_depan' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'sertifikat_belakang' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ], $messages);

        try {
            DB::beginTransaction();

            $rakernas = RakernasModel::findOrFail($id_rk);

            $rakernas->update([
                'tema' => $request->tema,
                'tempat' => $request->tempat,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'biaya' => $request->biaya,
                'fasilitas' => $request->fasilitas,
            ]);


            if ($request->hasFile('sertifikat_depan')) {
                $fileName = $this->uploadFile($request->file('sertifikat_depan'), 'uploads/rakernas');
                $rakernas->update([
                    'sertifikat_depan' => $fileName
                ]);
            }

            if ($request->hasFile('sertifikat_belakang')) {
                $fileName = $this->uploadFile($request->file('sertifikat_belakang'), 'uploads/rakernas');
                $rakernas->update([
                    'sertifikat_belakang' => $fileName
                ]);
            }


            $sertifikat = SertifikatModel::where('id_wb', $rakernas->id_rk)->first();

            if ($sertifikat) {
                $sertifikat->update([
                    'no_surat' => $request->no_surat,
                    'angkatan' => $request->angkatan,
                    'unit' => $request->unit,
                ]);
            } else {
                SertifikatModel::create([
                    'id_wb' => $sertifikat->id_rk,
                    'no_surat' => $request->no_surat,
                    'angkatan' => $request->angkatan,
                    'unit' => $request->unit,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', '!');
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Gagal menyimpan data: ' . $e->getMessage(), ['trace' => $e->getTrace()]);
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function hapus($id_rk)
    {
        try {
            DB::beginTransaction();

            // Hapus fasilitas terkait
            PendaftarRakernasModel::where('id_rk', $id_rk)->delete();

            // Hapus webinar utama
            $rakernas = RakernasModel::findOrFail($id_rk);
            $rakernas->delete();

            DB::commit();

            return redirect()->back()->with('success', '!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal hapus webinar: ' . $e->getMessage());
        }
    }
}
