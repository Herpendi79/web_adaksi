<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\AnggotaModel;
use App\Models\WebinarModel;
use App\Models\User;
use App\Models\PendaftarExtModel;
use App\Models\FasilitasModel;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('auth.login');
        } elseif ($request->isMethod('post')) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if ($user->role === 'admin' || $user->role === 'hukum') {
                    return redirect()->intended('admin/dashboard');
                } elseif ($user->role === 'anggota') {
                    $id_user = $user->id_user;
                    $getAnggota = AnggotaModel::where('id_user', $id_user)->first();
                    if ($getAnggota->status_anggota === 'pending') {
                        return redirect()->back()->withErrors([
                            'email' => 'Akun Anda masih dalam proses validasi. Silakan tunggu maksimal 2x24 jam sejak submit pendaftaran.',
                        ]);
                    }
                    if ($getAnggota->status_anggota === 'nonaktif') {
                        return redirect()->back()->withErrors([
                            'email' => 'Akun Anda telah Non Aktif. Silakan hubungi admin untuk informasi lebih lanjut.',
                        ]);
                    }
                    return redirect()->intended('anggota/dashboard');
                }
            } else {
                return redirect()->back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
            }
        }
    }

    public function forgetPassword(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('auth.forget_password');
        } elseif ($request->isMethod('post')) {
            $request->validate([
                'email' => 'required|email',
                'no_hp' => 'required|string',
            ]);
            $email = $request->input('email');
            $no_hp = $request->input('no_hp');
            $anggota = User::where('email', $email)->where('no_hp', $no_hp)->first();

            if (!$anggota) {
                return redirect()->back()->withErrors([
                    'email' => 'Email atau nomor HP tidak ditemukan.',
                ]);
            }
            // Logika untuk mengirim email reset password


            // bikin token reset password
            $token = bin2hex(random_bytes(4)); // Membuat token acak

            // ubah password
            $anggota->password = bcrypt($token); // Simpan token sebagai password baru
            $anggota->save();
            // kirim ke wa


            $message = "Halo " . $anggota->nama_anggota . "👋

Kami menerima permintaan untuk mengatur ulang password akun Anda di Sistem ADAKSI. Jika Anda tidak meminta ini, abaikan pesan ini.

Berikut adalah *password baru* Anda: $token

Silakan masuk ke akun Anda dengan password baru ini dan ubah password Anda segera setelah login sistem.
            
Terima kasih telah bergabung bersama ADAKSI. Mari bersama-sama berjuang untuk Indonesia Emas.

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
                    'target' => $anggota->no_hp,
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
            // arahkan ke login
            return redirect('/login')->with('success', 'Permintaan reset password berhasil. Silakan cek WhatsApp Anda untuk mendapatkan password baru.');
        }
    }

    //setting password admin
    public function setting()
    {
        $user = Auth::user()->load('admin');
        return view('admin_page.setting.index', compact('user'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
    }

    public function fasilitas(Request $request)
    {
        $email = $request->input('email');
        $token = $request->input('token');

        $pendaftar = PendaftarExtModel::with('webinar')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$pendaftar) {
            return redirect()->route('fasilitas')->withErrors([
                'email' => 'Email atau token tidak valid.',
            ]);
        }

        $id_wb = $pendaftar->id_wb;
        $fasilitas = FasilitasModel::where('id_wb', $id_wb)->get();

        session()->put('pendaftar', $pendaftar);
        session()->put('fasilitas', $fasilitas);

        return redirect()->route('fasilitas.result');
    }

    public function fasilitasResult()
    {
        if (!session()->has('pendaftar') || !session()->has('fasilitas')) {
            return redirect()->route('fasilitas');
        }

        $pendaftar = session('pendaftar');
        $fasilitas = session('fasilitas');

        return view('guest_page.fasilitas_download', compact('pendaftar', 'fasilitas'));
    }

    public function fasilitasSertifikat($id)
    {

        $pendaftar = session('pendaftar');

        $email = $pendaftar->email;
        $no_hp = $pendaftar->no_hp;

        $webinar = WebinarModel::with('pendaftar')->findOrFail($id);

        $pendaftar = PendaftarExtModel::where('id_wb', $id)
            ->where('email', $email)
            ->where('no_hp', $no_hp)
            ->first();

        return view('components.sertifikat', compact('webinar', 'pendaftar'));
    }

    public function clearSession()
    {
        session()->forget(['pendaftar', 'fasilitas']);
        return redirect('/daftar-anggota');
    }

    public function google_redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function google_callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::whereEmail($googleUser->email)->first();

        if (!$user) {
            return redirect('/login')->withErrors([
                'email' => 'Email tidak terdaftar. Silakan daftar anggota terlebih dahulu.',
            ]);
        }

        // Login manual tanpa password
        Auth::login($user);

        $user = Auth::user();
        if ($user->role === 'admin' || $user->role === 'hukum') {
            return redirect()->intended('admin/dashboard');
        } elseif ($user->role === 'anggota') {
            $id_user = $user->id_user;
            $getAnggota = AnggotaModel::where('id_user', $id_user)->first();

            if ($getAnggota->status_anggota === 'pending') {
                Auth::logout();
                return redirect('/login')->withErrors([
                    'email' => 'Akun Anda masih dalam proses validasi. Silakan tunggu maksimal 2x24 jam sejak submit pendaftaran.',
                ]);
            }

            if ($getAnggota->status_anggota === 'nonaktif') {
                Auth::logout();
                return redirect('/login')->withErrors([
                    'email' => 'Akun Anda telah Non Aktif. Silakan hubungi admin untuk informasi lebih lanjut.',
                ]);
            }

            return redirect()->intended('anggota/dashboard');
        }

        return redirect('/login')->withErrors([
            'email' => 'Role pengguna tidak dikenali.',
        ]);
    }
}
