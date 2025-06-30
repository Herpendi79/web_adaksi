<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengaturanModel;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $pengaturan = PengaturanModel::first();
        return view('admin_page.main.dashboard', compact('pengaturan'));
    }
    public function pengaturan()
    {
        $pengaturan = PengaturanModel::first();
        return view('pengaturan', compact('pengaturan'));
    }
    public function edit(Request $request)
    {
        $request->validate([
            'no_awal' => 'required|integer',
            'no_tengah' => 'required|integer',
        ]);

        $pengaturan = PengaturanModel::first();
        if ($pengaturan) {
            $pengaturan->update($request->only(['no_awal', 'no_tengah']));
        } else {
            PengaturanModel::create($request->only(['no_awal', 'no_tengah']));
        }

        return redirect()->route('dashboard')->with('success', 'Pengaturan berhasil diperbarui.');
    }
    
    public function setting()
    {
        $user = Auth::user()->load('anggota');
        return view('admin_page.main.setting', compact('user'));
    }

    public function edit_pass(Request $request)
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
             return redirect()->back()->with('success', 'Password berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui password: ' . $e->getMessage());
        }
    }
}
