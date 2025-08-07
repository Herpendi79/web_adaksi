<?php

namespace App\Http\Controllers;

// php spreetsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Mail\AnggotaValidateMail;
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



class RekeningController extends Controller
{

    public function showAllRekening(Request $request)
    {
        $rekening = RekeningModel::when($request->search, function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_bank', 'like', '%' . $request->search . '%')
                    ->orWhere('no_rek', 'like', '%' . $request->search . '%')
                    ->orWhere('atas_nama', 'like', '%' . $request->search . '%');
            });
        })
            ->orderBy('nama_bank', 'desc')
            ->paginate(10);

        return view('admin_page.rekening.index', compact('rekening'));
    }

    public function create()
    {
        return view('admin_page.rekening.create'); // atau view yang kamu gunakan
    }


    public function store(Request $request)
    {
        $messages = [
            'nama_bank.required' => 'Nama Bank wajib diisi.',
            'nama_bank.max' => 'Nama Bank maksimal 255 karakter.',

            'no_rek.required' => 'Nomor rekening wajib diisi.',
            'no_rek.numeric' => 'Nomor rekening wajib angka.',

            'atas_nama.required' => 'Atas Nama wajib diisi.',
            'atas_nama.max' => 'Atas Nama maksimal 255 karakter.',
        ];

        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'no_rek' => 'required|numeric',
            'atas_nama' => 'required|string|max:255',

        ], $messages);

        try {
            DB::beginTransaction();


            $rekening = RekeningModel::create([
                'nama_bank' => $request->nama_bank,
                'no_rek' => $request->no_rek,
                'atas_nama' => $request->atas_nama,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Rekening Berhasil Ditambahkan!');
        } catch (\Exception $e) {

            DB::rollBack();
            notify()->error('Gagal menyimpan data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $rekening = RekeningModel::where('id_rek', $id)
            ->firstOrFail();


        return view('admin_page.rekening.edit', compact('rekening'));
    }

    public function update(Request $request, $id)
    {

        $messages = [
            'nama_bank.required' => 'Nama Bank wajib diisi.',
            'nama_bank.max' => 'Nama Bank maksimal 255 karakter.',

            'no_rek.required' => 'Nomor rekening wajib diisi.',
            'no_rek.numeric' => 'Nomor rekening wajib angka.',

            'atas_nama.required' => 'Atas Nama wajib diisi.',
            'atas_nama.max' => 'Atas Nama maksimal 255 karakter.',
        ];

        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'no_rek' => 'required|numeric',
            'atas_nama' => 'required|string|max:255',

        ], $messages);
        try {
            $rekening = RekeningModel::findOrFail($id);

            $rekening->update([
                'nama_bank' => $request->nama_bank,
                'no_rek' => $request->no_rek,
                'atas_nama' => $request->atas_nama
            ]);

            $rekening->save();

            return redirect()->back()->with('success', 'Rekening berhasil diperbarui!');
        } catch (\Exception $e) {
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
