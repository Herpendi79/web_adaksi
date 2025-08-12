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
use Illuminate\Support\Facades\File;
use App\Models\AnggotaModel;
use App\Models\RekeningModel;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PendaftarRakernasExport;

class RakernasController extends Controller
{
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
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);

        $rakernass = RakernasModel::withCount(['pendaftar as pending_pendaftar_count' => function ($query) {
            $query->where('status', 'valid');
        }])
            ->paginate(10);


        $total_biaya = [];

        foreach ($rakernas as $rk) {
            $total = PendaftarRakernasModel::where('id_rk', $rk->id_rk)
                ->where('status', 'valid')
                ->get()
                ->sum(function ($pendaftar) use ($rk) {
                    return ($pendaftar->pengurus === 'Anggota Biasa')
                        ? ($rk->biaya_non_pengurus ?? 0)
                        : ($rk->biaya ?? 0);
                });

            $total_biaya[$rk->id_rk] = $total;
        }


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

        $rakernas->getCollection()->transform(function ($item) {
            $item->sisa_kuota = $item->hitungSisaKuota();
            $item->sisa_kuota_anggota_biasa = $item->hitungSisaKuotaAll(); // Tambahan untuk Blade
            return $item;
        });


        return view('admin_page.rakernas.index', compact('rakernas', 'total_biaya', 'valid', 'pending', 'jumlahPendaftarPerWebinar'));
    }

    public function pendaftar(Request $request, $id)
    {
        // Ambil data pendaftar rakernas
        $pendaftar_rakernas = PendaftarRakernasModel::where('id_rk', $id)
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->orWhereHas('anggota', function ($qa) use ($request) {
                        $qa->where('id_card', 'like', '%' . $request->search . '%')
                            ->orWhere('nama_anggota', 'like', '%' . $request->search . '%')
                            ->orWhere('homebase_pt', 'like', '%' . $request->search . '%')
                            ->orWhere('pengurus', 'like', '%' . $request->search . '%')
                            ->orWhere('provinsi', 'like', '%' . $request->search . '%');
                    });
                });
            })
            ->with(['anggota', 'user'])
            ->orderBy('status', 'asc')
            ->paginate(10);

        $data = $pendaftar_rakernas->first();

        // Ambil data rakernas untuk menghitung total biaya pending
        $rakernas = RakernasModel::withCount(['pendaftar as pending_pendaftar_count' => function ($query) {
            $query->where('status', 'valid');
        }])
            ->paginate(10);

        $total_masuk = [];

        foreach ($rakernas as $rk) {
            $total = PendaftarRakernasModel::where('id_rk', $rk->id_rk)
                ->where('status', 'valid')
                ->get()
                ->sum(function ($pendaftar) use ($rk) {
                    return ($pendaftar->pengurus === 'Anggota Biasa')
                        ? ($rk->biaya_non_pengurus ?? 0)
                        : ($rk->biaya ?? 0);
                });

            $total_masuk[$rk->id_rk] = $total;
        }


        // Hitung total ukuran baju masing-masing ukuran untuk rakernas terkait
        // Hitung ukuran_baju yang ADA DI TABEL, status 'valid' dan id_rk sesuai
        $ukuranBajuCounts = PendaftarRakernasModel::where('id_rk', $id)
            ->where('status', 'valid')
            ->whereNotNull('ukuran_baju')
            ->where('ukuran_baju', '!=', '-')
            ->selectRaw('ukuran_baju, COUNT(*) as total')
            ->groupBy('ukuran_baju')
            ->orderBy('ukuran_baju')
            ->pluck('total', 'ukuran_baju');



        return view('admin_page.rakernas.pendaftar', [
            'pendaftar_rakernas' => $pendaftar_rakernas,
            'rakernas' => $rakernas,
            'id' => $id,
            'total_masuk' => $total_masuk,
            'data' => $data,
            'ukuranBajuCounts' => $ukuranBajuCounts,
        ]);
    }

    public function create()
    {
        $rekening = RekeningModel::orderBy('nama_bank')->get();

        return view('admin_page.rakernas.create', compact('rekening'));
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
            $fotoPathServer = base_path('../public_html/uploads/foto_anggota/' . $fotoFile);
            $fotoPathServer2 = base_path('public/uploads/foto_anggota/' . $fotoFile);

            if (!$fotoFile || (!file_exists($fotoPathServer) && !file_exists($fotoPathServer2))) {
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
                    'ukuran_baju' => $pendaftar->ukuran_baju,
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
            'tanggal_tutup.required' => 'Tanggal selesai wajib diisi.',

            'biaya.required' => 'Biaya wajib diisi.',
            'biaya.numeric' => 'Biaya wajib angka.',

            'kuota.required' => 'Kuota wajib diisi.',
            'kuota.numeric' => 'Kuota wajib angka.',

            'biaya.required' => 'Biaya wajib diisi.',
            'biaya.numeric' => 'Biaya wajib angka.',

            'biaya_non_pengurus.required' => 'Biaya wajib diisi.',
            'biaya_non_pengurus.numeric' => 'Biaya wajib angka.',

            'fasilitas.required' => 'Fasilitas wajib diisi.',
            'fasilitas.string' => 'Fasilitas harus berupa teks.',
            'fasilitas.max' => 'Fasilitas maksimal 255 karakter.',

            'no_surat.required' => 'Nomor surat wajib diisi.',
            'angkatan.required' => 'Angkatan wajib diisi.',
            'unit.required' => 'Unit wajib diisi.',
            'sertifikat_depan.mimes' => 'Harus berupa JPG, JPEG, PNG',
            'sertifikat_belakang.mimes' => 'Harus berupa JPG, JPEG, PNG',

            'id_rek.required' => 'Rekening wajib dipilih.',
        ];

        // Sanitize biaya from "Rp 500.000" ➔ "500000"
        $request->merge([
            'biaya' => preg_replace('/[^\d]/', '', $request->biaya),
            'biaya_non_pengurus' => preg_replace('/[^\d]/', '', $request->biaya_non_pengurus)
        ]);

        $request->validate([
            'tema' => 'required|string|max:255',
            'tempat' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'biaya' => 'required|numeric',
            'biaya_non_pengurus' => 'required|numeric',
            'fasilitas' => 'required|string|max:255',
            'no_surat' => 'required|string',
            'angkatan' => 'required|string',
            'unit' => 'required|string',
            'sertifikat_depan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'sertifikat_belakang' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'id_rek' => 'required|exists:rekening,id_rek',
            'kuota' => 'required',
            'tanggal_tutup' => 'required',
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
                'biaya_non_pengurus' => $request->biaya_non_pengurus,
                'fasilitas' => $request->fasilitas,
                'sertifikat_depan' => $this->uploadFile($request->file('sertifikat_depan'), 'uploads/rakernas'),
                'sertifikat_belakang' => $this->uploadFile($request->file('sertifikat_belakang'), 'uploads/rakernas'),
                'id_rek' => $request->id_rek,
                'kuota' => $request->kuota,
                'tanggal_tutup' => $request->tanggal_tutup,
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

        $rekeningDipakaiIds = RakernasModel::whereNotNull('id_rek')->distinct()->pluck('id_rek');

        $rekeningDipakai = RekeningModel::whereIn('id_rek', $rekeningDipakaiIds)->get();

        $rekeningBelumDipakai = RekeningModel::whereNotIn('id_rek', $rekeningDipakaiIds)->get();

        // Gabungkan dan urutkan berdasarkan nama_bank
        $rekening = $rekeningDipakai->merge($rekeningBelumDipakai)->sortBy('nama_bank')->values();

        return view('admin_page.rakernas.edit', compact('rakernas', 'sertifikat', 'rekening'));
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
                        })
                        ->orWhereHas('AbsenPendaftar', function ($qp) use ($request) {
                            $qp->where('pengurus', 'like', '%' . $request->search . '%');
                        });
                });
            })
            ->with(['AbsenPendaftar.anggota'])
            ->with('AbsenPendaftar') // pastikan ini ada
            ->orderBy('kehadiran', 'asc')
            ->paginate(10);


        $rakernas = RakernasModel::findOrFail($id);

        // Ambil total absensi berdasarkan jenis pengurus
        $totalAbsensiByPengurus = AbsensiRakernasModel::whereHas('AbsenPendaftar', function ($q) use ($id) {
            $q->where('id_rk', $id);
        })
            ->selectRaw('pendaftar_rakernas.pengurus, COUNT(*) as total')
            ->join('pendaftar_rakernas', 'absensi_rakernas.id_prk', '=', 'pendaftar_rakernas.id_prk')
            ->groupBy('pendaftar_rakernas.pengurus')
            ->pluck('total', 'pengurus');


        $totalPendaftarRakernas = \App\Models\PendaftarRakernasModel::where('id_rk', $id)->count();

        // Total yang absen (unik per pendaftar, jika 1 pendaftar bisa absen lebih dari 1x, gunakan distinct)
        $totalAbsen = \App\Models\AbsensiRakernasModel::whereHas('AbsenPendaftar', function ($q) use ($id) {
            $q->where('id_rk', $id);
        })->distinct('id_prk')->count('id_prk');

        // Total yang tidak absen
        $totalTidakAbsen = $totalPendaftarRakernas - $totalAbsen;

        // Kirim ke view
        return view('admin_page.rakernas.absensi', compact(
            'absensi_rakernas',
            'rakernas',
            'totalAbsensiByPengurus',
            'totalPendaftarRakernas',
            'totalAbsen',
            'totalTidakAbsen'
        ));
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
            'tanggal_tutup.required' => 'Tanggal selesai wajib diisi.',

            'biaya.required' => 'Biaya wajib diisi.',
            'biaya.numeric' => 'Biaya wajib angka.',

            'kuota.required' => 'Kuota wajib diisi.',
            'kuota.numeric' => 'Kuota wajib angka.',

            'biaya_non_pengurus.required' => 'Biaya wajib diisi.',
            'biaya_non_pengurus.numeric' => 'Biaya wajib angka.',

            'fasilitas.required' => 'Fasilitas wajib diisi.',
            'fasilitas.string' => 'Fasilitas harus berupa teks.',
            'fasilitas.max' => 'Fasilitas maksimal 255 karakter.',

            'no_surat.required' => 'Nomor Surat wajib diisi.',
            'angkatan.required' => 'Angkatan wajib diisi.',
            'unit.required' => 'Unit wajib diisi.',
            'sertifikat_depan.mimes' => 'Harus file PDF.',
            'sertifikat_belakang.mimes' => 'Harus file PDF.',

            'id_rek.required' => 'Rekening wajib dipilih.',
        ];

        // Sanitize biaya from "Rp 500.000" ➔ "500000"
        $request->merge([
            'biaya' => preg_replace('/[^\d]/', '', $request->biaya),
            'biaya_non_pengurus' => preg_replace('/[^\d]/', '', $request->biaya_non_pengurus),
        ]);

        $request->validate([
            'tema' => 'required|string|max:255',
            'tempat' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'biaya' => 'required|numeric',
            'biaya_non_pengurus' => 'required|numeric',
            'fasilitas' => 'required|string|max:255',
            'no_surat' => 'required|string',
            'angkatan' => 'required|string',
            'unit' => 'required|string',
            'sertifikat_depan' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'sertifikat_belakang' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'id_rek' => 'required',
            'kuota' => 'required',
            'tanggal_tutup' => 'required',
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
                'biaya_non_pengurus' => $request->biaya_non_pengurus,
                'fasilitas' => $request->fasilitas,
                'id_rek' => $request->id_rek,
                'tanggal_tutup' => $request->tanggal_tutup,
                'kuota' => $request->kuota,
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

            return redirect()->back()->with('success', 'Rakernas berhasil diedit!');
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
    public function sertifikat_rakernas($id_prk)
    {


        $user = Auth::user();
        $id_user = $user->id_user;


        $pendaftar_rk = PendaftarRakernasModel::where('id_prk', $id_prk)
            ->findOrFail($id_prk);
        $id_rk = $pendaftar_rk->id_rk;

        $rakernas = RakernasModel::where('id_rk', $id_rk)
            ->findOrFail($id_rk);

        $pendaftar = AnggotaModel::where('id_user', $id_user)
            ->first();

        return view('components.sertifikat_rakernas', compact('rakernas', 'pendaftar', 'pendaftar_rk'));
    }

    public function exportPendaftar($id)
    {
        return Excel::download(new PendaftarRakernasExport($id), 'pendaftar_rakernas_id_' . $id . '.xlsx');
    }

    public function validasiPendaftar($id_prk)
    {

        try {
            DB::beginTransaction();

            $ValidDaftar = PendaftarRakernasModel::with([
                'anggota',
                'user'
            ])
                ->where('id_prk', $id_prk)
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

            return back()->with('success', 'Anda telah terdaftar!');
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Gagal validasi: ' . $e->getMessage());
            return back()->with('error', 'Gagal validasi: ' . $e->getMessage());
        }
    }
}
