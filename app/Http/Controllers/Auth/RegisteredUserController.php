<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Divisi;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // ambil semua data dari tabel 'master_divisi' menggunakan model Divisions
        $divisions = Divisi::all();

        // kirim data tersebut ke view dengan nama variabel divisions
        return view('auth.register', [
            'divisions' => $divisions
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            // 'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'id_divisi' => ['required', 'exists:master_divisi,id_divisi'],
            'g-recaptcha-response' => ['required', 'recaptcha'],
        ], [
            'g-recaptcha-response.required' => 'Anda harus mencentang kotak "I\'m not a robot".',
            'g-recaptcha-response.recaptcha' => 'Verifikasi CAPTCHA gagal. Silakan coba lagi.',
            'id_divisi.required' => 'Anda harus memilih divisi.',
            'password.regex' => 'Password harus memiliki minimal 8 karakter, kombinasi huruf besar, huruf kecil, angka, dan simbol.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            //'id_divisi' => 1,
            'id_divisi' => $request->id_divisi,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
