<?php

namespace App\Http\Controllers;

// php spreetsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Mail\AnggotaValidateMail;
use App\Models\WebinarModel;
use App\Models\FasilitasModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;



class WebinarController extends Controller
{
    
    public function index()
    {
        $webinars = WebinarModel::all();
        return view('webinar', compact('webinars'));
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

        return view('admin_page.webinar.index', compact('webinar'));
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
        $destination = base_path('../public_html/' . $path); // sesuaikan jika Laravel di luar public_html
    
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
            'sertifikat_depan' => 'nullable|file|mimes:pdf|max:2048',
            'sertifikat_belakang' => 'nullable|file|mimes:pdf|max:2048',
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
                    // ✅ Update fasilitas lama
                    $requestFasilitasIds[] = $item['id_fas']; // Simpan ID yang dikirim user
            
                    FasilitasModel::where('id_fas', $item['id_fas'])->update([
                        'nama' => $item['nama'] ?? '',
                        'link' => $item['link'] ?? '',
                    ]);
                } else {
                    // ✅ Tambah fasilitas baru
                    FasilitasModel::create([
                        'id_wb' => $webinar->id_wb,
                        'nama' => $item['nama'] ?? '',
                        'link' => $item['link'] ?? '',
                    ]);
                }
            }
            
            // ✅ Hapus fasilitas yang tidak ada lagi di request
            $toDelete = array_diff($existingFasilitasIds, $requestFasilitasIds);
            if (!empty($toDelete)) {
                FasilitasModel::whereIn('id_fas', $toDelete)->delete();
            }

            return redirect()->route('webinar.index')->with('success', 'Webinar berhasil diperbarui.');
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
}
 