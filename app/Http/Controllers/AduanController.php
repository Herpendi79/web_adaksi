<?php

namespace App\Http\Controllers;

// php spreetsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Mail\AnggotaValidateMail;
use App\Models\LampiranModel;
use App\Models\WebinarModel;
use App\Models\FasilitasModel;
use App\Models\PendaftarExtModel;
use App\Models\RekeningModel;
use App\Models\RakernasModel;
use App\Models\SertifikatModel;
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
use Illuminate\Support\Facades\File;
use App\Models\AduanModel;
use App\Models\AnggotaModel;
use App\Models\TanggapanModel;

class AduanController extends Controller
{
    public function showAllAduanOnAdmin(Request $request)
    {
        $aduan = AduanModel::with(['tanggapan', 'lampiran', 'anggota', 'user']) // <-- penting
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('kategori', 'like', '%' . $request->search . '%')
                        ->orWhere('judul', 'like', '%' . $request->search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $request->search . '%')
                        ->orWhere('kategori', 'like', '%' . $request->search . '%')
                        ->orWhere('nama_anggota', 'like', '%' . $request->search . '%')
                        ->orWhere('homebase_pt', 'like', '%' . $request->search . '%')
                        ->orWhere('status', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('status')
            ->paginate(10);

        return view('admin_page.aduan.index', compact('aduan'));
    }

    public function selesai($id)
    {
        try {
            DB::beginTransaction();

            $aduan = AduanModel::findOrFail($id);

            $aduan->update([
                'status'      => 'selesai',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Berhasil diselesaikan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    }

    public function showAllAduan(Request $request)
    {
        $aduan = AduanModel::with(['tanggapan', 'lampiran']) // <-- penting
            ->where('id_user', Auth::id())
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('kategori', 'like', '%' . $request->search . '%')
                        ->orWhere('judul', 'like', '%' . $request->search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $request->search . '%')
                        ->orWhere('status', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('created_at')
            ->paginate(10);

        return view('anggota_page.aduan.index', compact('aduan'));
    }

    public function create()
    {
        return view('anggota_page.aduan.create');
    }

    public function store(Request $request)
    {
        // Pesan kustom untuk validasi
        $messages = [
            'judul.required'       => 'Judul wajib diisi.',
            'judul.max'            => 'Judul maksimal 255 karakter.',
            'deskripsi.required'   => 'Deskripsi wajib diisi.',
            'kategori.required'    => 'Kategori wajib diisi.',
            'kategori.string'      => 'Kategori harus berupa teks.',
            'kategori.max'         => 'Kategori maksimal 255 karakter.',
            'lampiran.required'    => 'Minimal satu lampiran wajib diunggah.',
            'lampiran.array'       => 'Lampiran harus berupa array.',
            'lampiran.*.file'      => 'Setiap lampiran harus berupa file.',
            'lampiran.*.mimes'     => 'Lampiran harus berupa file JPG, JPEG, PNG, atau PDF.',
            'lampiran.*.max'       => 'Ukuran maksimal masing-masing lampiran adalah 2MB.',
        ];

        // Aturan validasi
        $rules = [
            'judul'        => 'required|string|max:255',
            'deskripsi'    => 'required|string|max:2000',
            'kategori'     => 'required|string|max:255',
            'lampiran'     => 'required|array',
            'lampiran.*'   => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        // Validasi request
        $validated = $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            $kategoriData = json_decode($validated['kategori']);
            $kategoriString = '';

            if (is_array($kategoriData)) {
                $kategoriValues = array_map(function ($tag) {
                    return $tag->value;
                }, $kategoriData);
                $kategoriString = implode(',', $kategoriValues);
            } else {
                // Jika formatnya bukan JSON, asumsikan itu string biasa
                $kategoriString = $validated['kategori'];
            }

            // Ambil ID user
            $id_user = $request->id_user ?? Auth::id();

            // Simpan data aduan
            $aduan = AduanModel::create([
                'id_user'    => $id_user,
                'judul'      => $validated['judul'],
                'deskripsi'  => $validated['deskripsi'],
                'kategori'   => $kategoriString, // <-- PENTING
                'status'     => 'pending',
            ]);

            // Pastikan aduan tersimpan
            if (!$aduan || !$aduan->id_ad) {
                throw new \Exception("Gagal menyimpan data aduan.");
            }

            // Simpan setiap lampiran (jika ada)
            if ($request->hasFile('lampiran')) {

                foreach ($request->file('lampiran') as $file) {
                    $filename = $this->uploadFilePendaftar($file, 'uploads/lampiran/', 'lampiran_');

                    if (!$filename) {
                        throw new \Exception("Gagal upload file lampiran.");
                    }

                    LampiranModel::create([
                        'id_ad'    => $aduan->id_ad,
                        'lampiran' => $filename,
                    ]);
                }
            }


            $nomorHpHukum = User::where('role', 'hukum')->pluck('no_hp');

            $message = "Halo *Tim Hukum & Advokasi* 👋,\n\n" .
                "Telah masuk aduan baru ke sistem dengan informasi sebagai berikut:\n\n" .
                "📝 *Judul:* " . $request->judul . "\n" .
                "✌️ *Login via*:\n" . config('app.url') . "\n\n" .
                "Untuk melihat lebih info lebih lengkap." .
                "Terima kasih telah bergabung bersama ADAKSI. 🇮🇩✨";

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
                    'target' => $nomorHpHukum, // pastikan format nomor sudah internasional (62xxxxx)
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

            DB::commit();
            return redirect()->route('aduan.index')->with('success', 'Aduan berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Catat log error
            \Log::error('Gagal menyimpan aduan: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan aduan. ' . $e->getMessage()]);
        }
    }

    public function store_tanggapan_admin(Request $request)
    {
        // Pesan kustom untuk validasi
        $messages = [
            'id_ad.required' => 'ID Aduan tidak boleh kosong.',
            'id_ad.exists' => 'ID Aduan tidak valid.',
            'isi_tanggapan.required' => 'Tanggapan wajib diisi.',
            'isi_tanggapan.max' => 'Tanggapan maksimal 255 karakter.',
            'lampiran.file' => 'Lampiran harus berupa file.',
            'lampiran.mimes' => 'Lampiran harus berupa file JPG, JPEG, PNG, atau PDF.',
            'lampiran.max' => 'Ukuran maksimal lampiran adalah 2MB.',
        ];

        // Aturan validasi
        $rules = [
            'id_ad' => 'required|exists:aduan,id_ad',
            'isi_tanggapan' => 'required|string|max:255',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        // Validasi request
        $validated = $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $pemilik = ($user->role === 'anggota') ? 'anggota' : 'admin';

            $filename = null;

            // Cek apakah ada file yang diunggah
            if ($request->hasFile('lampiran')) {
                $uploadedFile = $request->file('lampiran');
                $filename = $this->uploadFilePendaftar($uploadedFile, 'uploads/lampiran/', 'lampiran_');
                if (!$filename) {
                    throw new \Exception("Gagal upload file lampiran.");
                }
            }

            // Simpan data tanggapan
            TanggapanModel::create([
                'id_ad' => $validated['id_ad'],
                'isi_tanggapan' => $validated['isi_tanggapan'],
                'pemilik' => $pemilik,
                'lampiran' => $filename,
            ]);

            // Cek apakah ini tanggapan pertama untuk aduan ini
            // Jika belum ada tanggapan lain untuk aduan ini, perbarui status
            $existingTanggapanCount = TanggapanModel::where('id_ad', $validated['id_ad'])->count();

            if ($existingTanggapanCount === 1) {
                $aduan = AduanModel::findOrFail($validated['id_ad']);
                $aduan->update([
                    'status' => 'review',
                ]);
            }


            DB::commit();

            $aduan = AduanModel::findOrFail($request->id_ad);

            $nomorHpAnggota = User::where('id_user', $aduan->id_user)->first();
            $Anggota = AnggotaModel::where('id_user', $aduan->id_user)->first();

            $message = "Halo *" . $Anggota->nama_anggota . "* 👋,\n\n" .
                "Aduan anda telah direspon oleh Tim Hukum & Advokasi ADAKSI:\n\n" .
                "📝 Silakan login ke sistem untuk melihat tanggapan dari Tim.\n" .
                "✌️ *Login via*:\n" . config('app.url') . "\n\n" .
                "Berikan informasi yang lengkap dan jelas ya untuk menyelesaikan aduan Anda.\n\n" .
                "Terima kasih telah bergabung bersama ADAKSI. 🇮🇩✨";

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
                    'target' => $nomorHpAnggota->no_hp, // pastikan format nomor sudah internasional (62xxxxx)
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

            return redirect()->back()->with('success', 'Berhasil menyimpan tanggapan!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menyimpan tanggapan: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan tanggapan. ' . $e->getMessage()]);
        }
    }

    public function store_tanggapan(Request $request)
    {
        // Pesan kustom untuk validasi
        $messages = [
            'id_ad.required' => 'ID Aduan tidak boleh kosong.',
            'id_ad.exists' => 'ID Aduan tidak valid.',
            'isi_tanggapan.required' => 'Tanggapan wajib diisi.',
            'isi_tanggapan.max' => 'Tanggapan maksimal 255 karakter.',
            'lampiran.file' => 'Lampiran harus berupa file.',
            'lampiran.mimes' => 'Lampiran harus berupa file JPG, JPEG, PNG, atau PDF.',
            'lampiran.max' => 'Ukuran maksimal lampiran adalah 2MB.',
        ];

        // Aturan validasi
        $rules = [
            'id_ad' => 'required|exists:aduan,id_ad', // Tambahkan validasi id_ad
            'isi_tanggapan' => 'required|string|max:255',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        // Validasi request
        $validated = $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            // Mengambil peran pengguna yang sedang login secara langsung
            $user = Auth::user();
            $pemilik = ($user->role === 'anggota') ? 'anggota' : 'admin';

            $filename = null;

            // Cek apakah ada file yang diunggah
            if ($request->hasFile('lampiran')) {
                $uploadedFile = $request->file('lampiran');
                $filename = $this->uploadFilePendaftar($uploadedFile, 'uploads/lampiran/', 'lampiran_');
                if (!$filename) {
                    throw new \Exception("Gagal upload file lampiran.");
                }
            }

            // Simpan data tanggapan
            TanggapanModel::create([
                'id_ad' => $validated['id_ad'],
                'isi_tanggapan' => $validated['isi_tanggapan'],
                'pemilik' => $pemilik,
                'lampiran' => $filename, // Selalu berikan nilai, entah itu nama file atau null
            ]);

            DB::commit();

            $nomorHpHukum = User::where('role', 'hukum')->pluck('no_hp');
           // $aduan = AduanModel::findOrFail($request->id_ad);

            $aduan = AduanModel::where('id_ad', $request->id_ad)->first();

            $message = "Halo *Tim Hukum & Advokasi* 👋,\n\n" .
                "Telah masuk *Tanggapan* dari pesan yang anda kirim ke Anggota dengan informasi sebagai berikut:\n\n" .
                "📝 *Judul:* " . $aduan->judul . "\n" .
                "✌️ *Login via*:\n" . config('app.url') . "\n\n" .
                "Untuk melihat *Tanggapan* selengkapnya.\n" .
                "Terima kasih telah bergabung bersama ADAKSI. 🇮🇩✨";

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
                    'target' => $nomorHpHukum, // pastikan format nomor sudah internasional (62xxxxx)
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

            return redirect()->route('aduan.index')->with('success', 'Tanggapan berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menyimpan tanggapan: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan tanggapan. ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $aduan = AduanModel::where('id_ad', $id)
            ->firstOrFail();

        return view('anggota_page.aduan.edit', compact('aduan'));
    }

    public function uploadFilePendaftar($file, $path, $prefix = '')
    {
        $filename = $prefix . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $destination = public_path($path); // direktori dalam folder public

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        if ($file->move($destination, $filename)) {
            return $filename;
        } else {
            \Log::error("Gagal memindahkan file ke: " . $destination);
            return false;
        }

        return $filename;
    }

    public function update(Request $request, $id_ad)
    {

        $messages = [
            'judul.required'       => 'Judul wajib diisi.',
            'judul.max'            => 'Judul maksimal 255 karakter.',
            'deskripsi.required'   => 'Deskripsi wajib diisi.',
            'kategori.required'    => 'Kategori wajib diisi.',
            'kategori.string'      => 'Kategori harus berupa teks.',
            'kategori.max'         => 'Kategori maksimal 255 karakter.',
            'lampiran.array'       => 'Lampiran harus berupa array.',
            'lampiran.*.file'      => 'Setiap lampiran harus berupa file.',
            'lampiran.*.mimes'     => 'Lampiran harus berupa file JPG, JPEG, PNG, atau PDF.',
            'lampiran.*.max'       => 'Ukuran maksimal masing-masing lampiran adalah 2MB.',
        ];

        // Aturan validasi
        $rules = [
            'judul'        => 'required|string|max:255',
            'deskripsi'    => 'required|string|max:2000',
            'kategori'     => 'required|string|max:255',
            'lampiran'     => 'required|array',
            'lampiran.*'   => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        // Validasi request
        $validated = $request->validate($rules, $messages);
        $kategoriData = json_decode($validated['kategori']);
        $kategoriString = '';

        if (is_array($kategoriData)) {
            $kategoriValues = array_map(function ($tag) {
                return $tag->value;
            }, $kategoriData);
            $kategoriString = implode(',', $kategoriValues);
        } else {
            // Jika formatnya bukan JSON, asumsikan itu string biasa
            $kategoriString = $validated['kategori'];
        }

        try {
            DB::beginTransaction();
            $aduan = AduanModel::findOrFail($id_ad);

            $aduan->update([
                'judul'      => $validated['judul'],
                'deskripsi'  => $validated['deskripsi'],
                'kategori'   => $kategoriString, // <-- PENTING
            ]);

            // Simpan setiap lampiran (jika ada)
            if ($request->hasFile('lampiran')) {

                // 1. Dapatkan semua file lampiran lama yang terkait dengan aduan
                $oldLampiranFiles = LampiranModel::where('id_ad', $aduan->id_ad)->get();

                // 2. Hapus file-file lama dari server (pastikan path-nya benar)
                foreach ($oldLampiranFiles as $oldFile) {
                    // Asumsikan nama file tersimpan di kolom 'lampiran'
                    $pathToFile = public_path('uploads/lampiran/' . $oldFile->lampiran);
                    if (File::exists($pathToFile)) {
                        File::delete($pathToFile);
                    }
                }

                // 3. Hapus semua entri lampiran lama dari database
                LampiranModel::where('id_ad', $aduan->id_ad)->delete();

                // 4. Simpan file-file baru yang diunggah
                foreach ($request->file('lampiran') as $file) {
                    $filename = $this->uploadFilePendaftar($file, 'uploads/lampiran/', 'lampiran_');

                    if (!$filename) {
                        throw new \Exception("Gagal upload file lampiran.");
                    }

                    // 5. Buat entri baru di database untuk setiap file yang diunggah
                    LampiranModel::create([
                        'id_ad' => $aduan->id_ad,
                        'lampiran' => $filename,
                        // Tambahkan kolom lain jika ada, misal 'id_user'
                    ]);
                }
            }


            DB::commit();

            return redirect()->back()->with('success', 'Aduan berhasil diedit!');
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Gagal menyimpan data: ' . $e->getMessage(), ['trace' => $e->getTrace()]);
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function hapus($id)
    {
        try {
            DB::beginTransaction();

            // Hapus fasilitas terkait
            LampiranModel::where('id_ad', $id)->delete();
            AduanModel::where('id_ad', $id)->delete();

            $lampiranList = LampiranModel::where('id_ad', $id)->get();

            foreach ($lampiranList as $lampiran) {
                $filePath = public_path('uploads/lampiran/' . $lampiran->lampiran);

                if (File::exists($filePath) && is_file($filePath)) {
                    File::delete($filePath);
                }

                $lampiran->delete();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal hapus webinar: ' . $e->getMessage());
        }
    }
}
