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
                // PERBAIKAN: Gunakan status huruf kecil yang konsisten
                ->whereIn('status', ['approved', 'pending'])
                ->pluck('mobil_id')
                ->unique();

            $kendaraansTersedia = Kendaraan::whereNotIn('mobil_id', $conflictingCarIds)
                ->get();
        }

        return view('dashboard', [
            'kendaraans' => $kendaraansTersedia
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi (dibuat lebih spesifik)
        $request->validate([
            'mobil_id' => 'required|exists:master_kendaraan,mobil_id',
            'tanggal_mulai' => 'required|date_format:Y-m-d|after_or_equal:today|before_or_equal:+1 week',
            'tanggal_selesai' => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai',
        ], [
            // Pesan error kustom
            'tanggal_selesai.after_or_equal' => 'Tanggal Selesai harus sama atau setelah Tanggal Mulai.',
            'tanggal_mulai.before_or_equal' => 'Tanggal Selesai harus sama atau setelah Tanggal Mulai.'
        ]);

        // 2. Ambil ID user
        $userId = Auth::id();

        // 3. Simpan data (PERBAIKAN DI SINI)
        Booking::create([
            'mobil_id' => $request->mobil_id,
            'user_id' => $userId,
            'km_awal' => null,
            'km_akhir' => null,
            
            'tanggal_mulai' => Carbon::parse($request->tanggal_mulai)->startOfDay(),
            'tanggal_selesai' => Carbon::parse($request->tanggal_selesai)->endOfDay(),
            
            'status' => 'pending', 
        ]);

        // 4. Redirect (Arahkan ke 'history' agar user bisa lihat statusnya)
        return redirect()->route('history')->with('success', 'Kendaraan berhasil dipesan! Tunggu persetujuan Admin.');
    }

    public function history(Request $request)
    {
        // Kode 'history' Anda sudah benar (asumsi 'nopol' sudah benar)
        $filters = $request->query();

        $query = Booking::where('user_id', Auth::id())
            ->with(['kendaraan']) // 'kendaraan' BUKAN 'user', 'kendaraan'
            ->latest('tanggal_mulai');

        // Filter Tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $reqMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $reqSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();
            $query->whereBetween('tanggal_mulai', [$reqMulai, $reqSelesai]);
        }

        // Filter Kendaraan
        if ($request->filled('nama_kendaraan')) {
            $query->whereHas('kendaraan', function ($q) use ($request) {
                $q->where('nama_kendaraan', 'like', '%' . $request->nama_kendaraan . '%');
            });
        }

        // Filter Nopol (Pastikan 'nopol' adalah nama kolom di DB)
        // Filter Nopol (KODE BARU YANG BENAR)
        // Pastikan ini 'nomor_polisi' agar cocok dengan name di form
        if ($request->filled('nomor_polisi')) {
            $query->whereHas('kendaraan', function ($q) use ($request) {
                // Pastikan 'nopol' ini adalah nama kolom di database Anda
                $q->where('nopol', 'like', '%' . $request->nomor_polisi . '%');
            });
        }

        $bookings = $query->paginate(10)->appends($filters);

        return view('history', [
            'bookings' => $bookings
        ]);
    }

    public function startBooking(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return redirect()->route('history')->with('error', 'Silahkan Login untuk memulai Booking');
        }

        // PERBAIKAN: Gunakan status huruf kecil
        if ($booking->status !== 'approved') {
            return redirect()->route('history')->with('error', 'Booking belum disetujui');
        }

        // PERBAIKAN: Gunakan status huruf kecil
        $last_km = Booking::where('mobil_id', $booking->mobil_id)
            ->where('status', 'finish')
            ->orderby('km_akhir', 'desc')
            ->first();

        $minKm = $last_km ? $last_km->km_akhir : 0;

        $request->validate([
            'km_awal' => 'required|numeric|min:' . $minKm,
        ]);

        $booking->update([
            'km_awal' => $request->km_awal
        ]);

        return redirect()->route('history')->with('success', 'Booking dimulai. Selamat berkendara!');
    }

    public function finishBooking(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return redirect()->route('history')->with('error', 'Silahkan Login untuk memulai Booking');
        }
        if (is_null($booking->km_awal)) {
            return redirect()->route('history')->with('error', 'Anda harus memasukan KM Awal terlebih dahulu!');
        }

        $request->validate([
            'km_akhir' => 'required|numeric|min:' . $booking->km_awal,
        ]);

        try {
            // PERBAIKAN: Hapus 'tanggal_selesai' => now()
            $booking->update([
                'status' => 'finish', // PERBAIKAN: Gunakan status huruf kecil
                'km_akhir' => $request->km_akhir,
                // 'tanggal_kembali' => now() // (Gunakan ini jika Anda punya kolom 'tanggal_kembali')
            ]);

            return redirect()->route('history')->with('success', 'Booking selesai. Terima kasih');
        } catch (\Exception $e) {
            return redirect()->route('history')->with('error', 'Gagal menyelesaikan booking. Silahkan coba lagi.');
        }
    }
}
