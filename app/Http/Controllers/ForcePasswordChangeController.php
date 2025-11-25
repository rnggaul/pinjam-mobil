<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChangeController extends Controller
{
    // 1. Menampilkan Form Ganti Password
    public function edit()
    {
        return view('auth.force-change-password');
    }

    // 2. Memproses Ganti Password
    public function update(Request $request)
    {
        // Validasi Ketat Sesuai Request Kamu
        $request->validate([
            'password' => [
                'required', 
                'confirmed', 
                'min:8', 
                'regex:/[a-z]/',      // Huruf kecil
                'regex:/[A-Z]/',      // Huruf besar
                'regex:/[0-9]/',      // Angka
                'regex:/[@$!%*#?&]/', // Simbol unik
            ],
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, kecil, angka, dan simbol.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        /**@var \App\Models\User $user */
        $user = Auth::user();

        // Update password & matikan status 'must_change_password'
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false 
        ]);

        return redirect()->route('dashboard')->with('status', 'Password berhasil diperbarui! Silakan gunakan password baru untuk login selanjutnya.');
    }
}