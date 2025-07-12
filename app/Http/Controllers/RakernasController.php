<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RakernasModel;
use App\Models\PendaftarRakernasModel;
use Illuminate\Support\Facades\DB;

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
            $query->where('status', 'pending');
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
            $query->where('status', 'pending');
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

            $ValidDaftar = PendaftarRakernasModel::where('id_prk', $id)
                ->firstOrFail();

                $ValidDaftar->status = 'valid';
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
        
📝 Akses melalui link berikut ya:
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

                $pendaftar = PendaftarExtModel::findOrFail($id);

                if ($pendaftar->bukti_tf) {
                    $filePath = public_path('uploads/bukti_tf_pendaftar/' . $pendaftar->bukti_tf);
                    if (File::exists($filePath) && is_file($filePath)) {
                        File::delete($filePath);
                    }
                }

                PendaftarExtModel::where('id_pwe', $ValidDaftar->id_pwe)->delete();

                $totalPendaftar = PendaftarExtModel::where('id_wb', $id_wb)->count();

                if ($totalPendaftar === 0) {
                    return redirect('admin/webinar')->with('info', 'Tidak ada data pendaftar lagi.');
                }
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
            return redirect()->back()->with('success', '!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Berhasil Divalidasi', $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin_page.rakernas.create');
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


        return view('admin_page.rakernas.edit', compact('rakernas'));
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
