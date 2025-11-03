<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    //
    public $timestamps = false;

    public function index(Request $request)
    {
        $tanggal_mulai_input = $request->input('tanggal_mulai');
        $tanggal_selesai_input = $request->input('tanggal_selesai');

        $kendaraansTersedia = collect();

        if ($tanggal_mulai_input && $tanggal_selesai_input) {

            $reqMulai = Carbon::parse($tanggal_mulai_input)->startOfDay();
            $reqSelesai = Carbon::parse($tanggal_selesai_input)->endOfDay();

            // 1. Ambil ID mobil yang BENTROK (menggunakan model 'Booking')
            $conflictingCarIds = Booking::where(function ($query) use ($reqMulai, $reqSelesai) {
                // Logika Overlap (Bentrok):
                // (Mulai_A < Selesai_B) AND (Selesai_A > Mulai_B)
                $query->where('tanggal_mulai', '<=', $reqSelesai)
                    ->where('tanggal_selesai', '>=', $reqMulai);
            })
                // Hanya cek booking yang masih aktif
                ->whereIn('status', ['approved','pending'])
                ->pluck('mobil_id')
                ->unique();


            // 2. Ambil semua kendaraan KECUALI yang ID-nya bentrok
            //    (Diperbarui: menggunakan 'mobil_id' sebagai primary key)
            $kendaraansTersedia = Kendaraan::whereNotIn('mobil_id', $conflictingCarIds)
                ->get();
        }

        // 3. Kirim data ke view
        return view('dashboard', [
            'kendaraans' => $kendaraansTersedia
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi data yang dikirim dari form
        $request->validate([
            'mobil_id' => 'required|exists:master_kendaraan,mobil_id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        // 2. Ambil ID user yang sedang login
        $userId = Auth::id();

        // 3. Simpan data booking baru
        Booking::create([
            'mobil_id' => $request->mobil_id,
            'user_id' => $userId,
            'km_awal' => null,
            'km_akhir' => null,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => 'Pending', // Status default saat pertama kali memesan
        ]);

        // 4. Redirect ke halaman 'history' (atau 'dashboard') dengan pesan sukses
        // (Pastikan Anda punya route 'history.index' nantinya)
        return redirect()->route('dashboard')->with('success', 'Kendaraan berhasil dipesan! Tunggu persetujuan Admin.');
    }

    public function history()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with('kendaraan')
            ->latest('tanggal_mulai')
            ->get();

            return view('history', [
                'bookings' => $bookings
            ]);
    }

    public function startBooking(Request $request, Booking $booking)
    {
        // 1 pastikan booking milik user yang sedang login
        if ($booking->user_id !== Auth::id()){
            return redirect()->route('history')->with('error', 'Silahkan Login untuk memulai Booking');
        }

        // 2 hanya boleh status approved
        if ($booking->status !== 'approved'){
            return redirect()->route('history')->with('error', 'Booking belum disetujui');
        }

        // 3 validasi input km_awal
        $last_km =  Booking::where('mobil_id', $booking->mobil_id)
                            ->where('status', 'finish')
                            ->orderby('km_akhir', 'desc')
                            ->first();

        $minKm = $last_km ? $last_km->km_akhir : 0;

        $request->validate([
            'km_awal' => 'required|numeric|min:'.$minKm,
        ]);

        // 4 update booking
        $booking->update([
            'km_awal' => $request->km_awal
        ]);

        return redirect()->route('history')->with('success', 'Booking dimulai. Selamat berkendara!');
    }

    public function finishBooking(Request $request, Booking $booking)
    {
        // 1 pastikan booking milik user yang sedang login
        if ($booking->user_id !== Auth::id()){
            return redirect()->route('history')->with('error', 'Silahkan Login untuk memulai Booking');
        }

        // 2 memastikan km awal sudah diisi
        if (is_null($booking->km_awal)){
            return redirect()->route('history')->with('error', 'Anda harus memasukan KM Awal terlebih dahulu!');
        }

        // 3 validasi input km_akhir
        $request->validate([
            'km_akhir' => 'required|numeric|min:'.$booking->km_awal,    
        ]);

        // 4 update booking
        try{
            $booking->update([
                'status' => 'finish',
                'km_akhir' => $request->km_akhir,
                'tanggal_selesai' => now()
            ]);

            return redirect()->route('history')->with('success', 'Booking selesai. Terima kasih');
        } catch (\Exception $e) {
            return redirect()->route('history')->with('error', 'Gagal menyelesaikan booking. Silahkan coba lagi.');
        }
    }
}
