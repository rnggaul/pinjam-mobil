<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Kendaraan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
    /**
     * Menampilkan dashboard security dengan filter.
     * Hanya menampilkan booking yang 'approved' atau 'berjalan'.
     */
    public function index(Request $request)
    {
        $filters = $request->query();

        // 1. Mulai kueri HANYA untuk status 'approved' (menunggu KM awal)
        //    atau 'berjalan' (menunggu KM akhir)
        $query = Booking::whereIn('status', ['approved'])
                            ->with(['user', 'kendaraan'])
                            ->latest('tanggal_mulai');

        // 2. Terapkan filter (LOGIKA YANG SAMA DENGAN FILTER ANDA)
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

        // (Ganti 'nopol' dengan 'nomor_polisi' jika perlu)
        if ($request->filled('nopol')) {
            $query->whereHas('kendaraan', function ($q) use ($request) {
                $q->where('nopol', 'like', '%' . $request->nopol . '%');
            });
        }

        if ($request->filled('name')){
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        // 3. Eksekusi
        $bookings = $query->paginate(10)->appends($filters);

        return view('security.dashboard', compact('bookings'));
    }

    /**
     * Menyimpan KM Awal (dipindahkan dari BookingController)
     * Kita juga tambahkan status baru 'berjalan'
     */
    public function startBooking(Request $request, Booking $booking)
    {
        // 1. Otorisasi (Hanya boleh jika status 'approved')
        if ($booking->status !== 'approved') {
             return redirect()->route('security.dashboard')->with('error', 'Booking ini belum disetujui atau sudah selesai.');
        }

        // 2. Validasi input KM Awal (berdasarkan riwayat terakhir)
        $last_km = Booking::where('mobil_id', $booking->mobil_id)
                           ->where('status', 'finish')
                           ->orderby('km_akhir', 'desc')
                           ->first();
        
        $minKm = $last_km ? $last_km->km_akhir : 0;

        $request->validate([
            'km_awal' => 'required|numeric|min:' . $minKm,
        ], [
            'km_awal.min' => 'KM Awal tidak boleh lebih rendah dari KM terakhir (:min KM).'
        ]);

        // 3. Update booking (Status berubah menjadi 'berjalan')
        $booking->update([
            'km_awal' => $request->km_awal,
            'status' => 'berjalan' // <-- STATUS BARU
        ]);
        
        return redirect()->route('security.dashboard')->with('success', 'Perjalanan untuk Booking #'.$booking->booking_id.' dimulai.');
    }

    /**
     * Menyimpan KM Akhir (dipindahkan dari BookingController)
     */
    public function finishBooking(Request $request, Booking $booking)
    {
        // 1. Validasi (Pastikan KM Awal sudah diisi dulu!)
        if (is_null($booking->km_awal)) {
             return redirect()->route('security.dashboard')->with('error', 'KM Awal belum diisi untuk booking ini.');
        }

        // 2. Validasi input KM Akhir
        $request->validate([
            'km_akhir' => 'required|numeric|min:' . $booking->km_awal,
        ], [
            'km_akhir.min' => 'KM Akhir tidak boleh lebih rendah dari KM Awal ('.$booking->km_awal.' KM).'
        ]);

        // 3. Update booking
        try {
            $booking->update([
                'status' => 'finish',
                'km_akhir' => $request->km_akhir,
                // 'tanggal_kembali' => now() // (Opsional jika Anda punya kolom ini)
            ]);

            return redirect()->route('security.dashboard')->with('success', 'Booking #'.$booking->booking_id.' telah diselesaikan.');
        
        } catch (\Exception $e) {
            return redirect()->route('security.dashboard')->with('error', 'Gagal menyelesaikan booking. Coba lagi.');
        }
    }
}