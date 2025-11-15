<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // HAPUS 'public $timestamps = false;' DARI SINI
    // Pindahkan ke app/Models/Booking.php

    public function index(Request $request)
    {
        // 👇 TAMBAHKAN BLOK INI (PENTING) 👇
        // Jika yang login BUKAN 'user', tendang mereka
        if (Auth::user()->role == 'security') {
            return redirect()->route(Auth::user()->role . '.dashboard')->with('error', 'Anda tidak bisa melakukan booking.');
        }
        if (Auth::user()->role == 'admin') {
            return redirect()->route(Auth::user()->role . '.index')->with('error', 'Anda tidak bisa melakukan booking.');
        }
        // --- Akhir Blok ---

        $tanggal_mulai_input = $request->input('tanggal_mulai');
        $tanggal_selesai_input = $request->input('tanggal_selesai');
        $kendaraansTersedia = collect();

        if ($tanggal_mulai_input && $tanggal_selesai_input) {
            $reqMulai = Carbon::parse($tanggal_mulai_input)->startOfDay();
            $reqSelesai = Carbon::parse($tanggal_selesai_input)->endOfDay();

            $conflictingCarIds = Booking::where(function ($query) use ($reqMulai, $reqSelesai) {
                $query->where('tanggal_mulai', '<=', $reqSelesai)
                      ->where('tanggal_selesai', '>=', $reqMulai);
            })
            ->whereIn('status', ['approved', 'pending'])
            ->pluck('mobil_id')
            ->unique();

            $kendaraansTersedia = Kendaraan::whereNotIn('mobil_id', $conflictingCarIds)->get();
        }

        return view('dashboard', [
            'kendaraans' => $kendaraansTersedia
        ]);
    }

    public function store(Request $request)
    {
        // 👇 TAMBAHKAN BLOK INI (PENTING) 👇
        // Keamanan lapis kedua, blokir aksi 'store'
        if (Auth::user()->role == 'security') {
            return redirect()->route('dashboard')->with('error', 'Role Anda tidak diizinkan untuk membuat booking.');
        }
        if (Auth::user()->role == 'admin') {
            return redirect()->route('dashboard')->with('error', 'Role Anda tidak diizinkan untuk membuat booking.');
        }
        // --- Akhir Blok ---

        // 1. Validasi
        $request->validate([
            'mobil_id' => 'required|exists:master_kendaraan,mobil_id',
            'tanggal_mulai' => 'required|date_format:Y-m-d|after_or_equal:today|before_or_equal:+1 week',
            'tanggal_selesai' => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal Selesai harus sama atau setelah Tanggal Mulai.',
            'tanggal_mulai.before_or_equal' => 'Anda hanya dapat memesan maksimal 7 hari dari sekarang.'
        ]);

        // 2. Ambil ID user
        $userId = Auth::id();

        // 3. Simpan data booking
        Booking::create([
            'mobil_id' => $request->mobil_id,
            'user_id' => $userId,
            'km_awal' => null,
            'km_akhir' => null,
            'tanggal_mulai' => Carbon::parse($request->tanggal_mulai)->startOfDay(),
            'tanggal_selesai' => Carbon::parse($request->tanggal_selesai)->endOfDay(),
            'status' => 'pending', 
        ]);

        // 4. Redirect
        return redirect()->route('history')->with('success', 'Kendaraan berhasil dipesan! Tunggu persetujuan Admin.');
    }

    public function history(Request $request)
    {
        // 👇 TAMBAHKAN BLOK INI (PENTING) 👇
        // Pastikan hanya 'user' yang bisa melihat history ini
        if (Auth::user()->role == 'security') {
            return redirect()->route(Auth::user()->role . '.dashboard')->with('error', 'Halaman ini hanya untuk user.');
        }
        if (Auth::user()->role == 'admin') {
            return redirect()->route(Auth::user()->role . '.index')->with('error', 'Halaman ini hanya untuk user.');
        }
        // --- Akhir Blok ---
        
        $filters = $request->query();

        $query = Booking::where('user_id', Auth::id())
                            ->with(['kendaraan'])
                            ->latest('tanggal_mulai');

        // (Filter-filter Anda sudah benar)
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $reqMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $reqSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();
            $query->whereBetween('tanggal_mulai', [$reqMulai, $reqSelesai]);
        }
        if ($request->filled('nama_kendaraan')) {
            $query->whereHas('kendaraan', function ($q) use ($request) {
                $q->where('nama_kendaraan', 'like', '%' . $request->nama_kendaraan . '%');
            });
        }
        if ($request->filled('nomor_polisi')) {
             $query->whereHas('kendaraan', function ($q) use ($request) {
                $q->where('nopol', 'like', '%' . $request->nomor_polisi . '%');
            });
        }

        $bookings = $query->paginate(10)->appends($filters);

        return view('history', [
            'bookings' => $bookings
        ]);
    }

    // Method 'startBooking' dan 'finishBooking' sudah dihapus (Diberi komentar)
    // Ini sudah benar
}