<?php

namespace App\Http\Controllers;

// php spreetsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Mail\AnggotaValidateMail;
use App\Models\BiayaModel;
use App\Models\RekeningModel;
use App\Models\WebinarModel;
use App\Models\FasilitasModel;
use App\Models\PendaftarExtModel;
use App\Models\PendaftarRakernasModel;
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



class BiayaController extends Controller
{

    public function showAllBiaya(Request $request)
    {
        $biaya = BiayaModel::when($request->search, function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('keterangan', 'like', '%' . $request->search . '%')
                    ->orWhere('nominal', 'like', '%' . $request->search . '%');
            });
        })
            ->orderBy('berlaku_mulai', 'asc')
            ->paginate(10);

        // Ambil enum values dari kolom keterangan pada tabel biaya
        $type = DB::select("SHOW COLUMNS FROM biaya WHERE Field = 'keterangan'")[0]->Type;
        preg_match('/^enum\\((.*)\\)$/', $type, $matches);
        $biayamodal = [];
        if (isset($matches[1])) {
            $biayamodal = array_map(function ($value) {
                return trim($value, "'");
            }, explode(',', $matches[1]));
        }

        return view('admin_page.biaya.index', compact('biaya', 'biayamodal'));
    }

    public function store(Request $request)
    {
        $messages = [
            'keterangan.required' => 'Keterangan wajib dipilih.',
            'keterangan.in' => 'Keterangan tidak valid.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.regex' => 'Nominal harus berupa angka tanpa titik/koma.',
            'berlaku_mulai.date' => 'Tanggal mulai tidak valid.',
            'berlaku_sampai.date' => 'Tanggal sampai tidak valid.',
            'berlaku_sampai.after_or_equal' => 'Tanggal sampai harus setelah atau sama dengan tanggal mulai.',
        ];

        // Ambil enum values dari kolom keterangan pada tabel biaya
        $type = DB::select("SHOW COLUMNS FROM biaya WHERE Field = 'keterangan'")[0]->Type;
        preg_match('/^enum\\((.*)\\)$/', $type, $matches);
        $biayamodal = [];
        if (isset($matches[1])) {
            $biayamodal = array_map(function ($value) {
                return trim($value, "'");
            }, explode(',', $matches[1]));
        }

        $request->validate([
            'keterangan' => 'required|in:' . implode(',', $biayamodal),
            'nominal' => ['required'],
            'berlaku_mulai' => 'nullable|date',
            'berlaku_sampai' => 'nullable|date|after_or_equal:berlaku_mulai',
        ], $messages);

        // Validasi overlap tanggal untuk keterangan yang sama
        if ($request->keterangan && $request->berlaku_mulai && $request->berlaku_sampai) {
            $overlap = BiayaModel::where('keterangan', $request->keterangan)
                ->where(function ($q) use ($request) {
                    $q->where(function ($q2) use ($request) {
                        $q2->where('berlaku_mulai', '<=', $request->berlaku_sampai)
                            ->where('berlaku_sampai', '>=', $request->berlaku_mulai);
                    });
                })
                ->exists();
            if ($overlap) {
                return redirect()->back()->withInput()->withErrors(['berlaku_mulai' => 'Tanggal berlaku sudah terdaftar untuk keterangan yang sama.']);
            }
        }

        try {
            DB::beginTransaction();

            // Hilangkan format rupiah jika ada
            $nominal = preg_replace('/[^0-9]/', '', $request->nominal);

            BiayaModel::create([
                'keterangan' => $request->keterangan,
                'nominal' => $nominal,
                'berlaku_mulai' => $request->berlaku_mulai,
                'berlaku_sampai' => $request->berlaku_sampai,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Biaya berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            notify()->error('Gagal menyimpan data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'keterangan.required' => 'Keterangan wajib dipilih.',
            'keterangan.in' => 'Keterangan tidak valid.',
            'nominal.required' => 'Nominal wajib diisi.',
            'berlaku_mulai.date' => 'Tanggal mulai tidak valid.',
            'berlaku_sampai.date' => 'Tanggal sampai tidak valid.',
            'berlaku_sampai.after_or_equal' => 'Tanggal sampai harus setelah atau sama dengan tanggal mulai.',
        ];

        // Ambil enum values dari kolom keterangan pada tabel biaya
        $type = DB::select("SHOW COLUMNS FROM biaya WHERE Field = 'keterangan'")[0]->Type;
        preg_match('/^enum\\((.*)\\)$/', $type, $matches);
        $biayamodal = [];
        if (isset($matches[1])) {
            $biayamodal = array_map(function ($value) {
                return trim($value, "'");
            }, explode(',', $matches[1]));
        }

        $request->validate([
            'keterangan' => 'required|in:' . implode(',', $biayamodal),
            'nominal' => ['required'],
            'berlaku_mulai' => 'nullable|date',
            'berlaku_sampai' => 'nullable|date|after_or_equal:berlaku_mulai',
        ], $messages);

        // Validasi overlap tanggal untuk keterangan yang sama, kecuali data ini sendiri
        if ($request->keterangan && $request->berlaku_mulai && $request->berlaku_sampai) {
            $overlap = BiayaModel::where('keterangan', $request->keterangan)
                ->where('id', '!=', $id)
                ->where(function ($q) use ($request) {
                    $q->where(function ($q2) use ($request) {
                        $q2->where('berlaku_mulai', '<=', $request->berlaku_sampai)
                            ->where('berlaku_sampai', '>=', $request->berlaku_mulai);
                    });
                })
                ->exists();
            if ($overlap) {
                return redirect()->back()->withInput()->withErrors(['berlaku_mulai' => 'Tanggal berlaku sudah terdaftar untuk keterangan yang sama.']);
            }
        }

        try {
            DB::beginTransaction();

            // Hilangkan format rupiah jika ada
            $nominal = preg_replace('/[^0-9]/', '', $request->nominal);

            $biaya = BiayaModel::findOrFail($id);
            $biaya->keterangan = $request->keterangan;
            $biaya->nominal = $nominal;
            $biaya->berlaku_mulai = $request->berlaku_mulai;
            $biaya->berlaku_sampai = $request->berlaku_sampai;
            $biaya->save();

            DB::commit();
            return redirect()->back()->with('success', 'Biaya berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            notify()->error('Gagal menyimpan data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    public function hapus($id)
    {
        try {
            DB::beginTransaction();

            RekeningModel::where('id_rek', $id)->delete();

            DB::commit();

            return redirect()->back()->with('success', '!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal hapus webinar: ' . $e->getMessage());
        }
    }
}
