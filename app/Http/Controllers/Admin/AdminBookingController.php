<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsHistoryExport;

class AdminBookingController extends Controller
{
    public function index()
    {
        // mengambil semua booking dengan status 'pending' lalu mengirimkan ke view
        $pendingBookings = Booking::where('status', 'pending')
                                    ->with(['user', 'kendaraan'])
                                    ->latest('tanggal_mulai')
                                    ->paginate(10);

        return view('admin.booking.index', compact('pendingBookings'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        // 1 validasi input dari form
        $request->validate([
            'status' => 'required|in:approved,rejected',

            'note' => 'required_if:status,rejected|string|nullable|max:1000',
        ],[
            'note.required_if' => 'Alasan penolakan wajib diisi jika me-reject booking.'
        ]);

        // 2 update status booking
        $booking->update([
            'status' => $request->status
        ]);

        // 3 redirect kembali dengan pesan sukses
        return redirect()->route('admin.booking.index')->with('success', 'Status booking berhasil diperbarui.');
    }

    public function history(Request $request)
    {
        // 1. Ambil semua input filter dari URL.
        // request()->query() mengambil semua parameter GET (misal: ?nopol=B123)
        $filters = $request->query();

        // 2. Mulai bangun kueri, jangan langsung ->get()
        $query = Booking::where('status', '!=', 'pending')
                            ->with(['user', 'kendaraan'])
                            ->latest('tanggal_mulai');

        // 3. Terapkan filter HANYA JIKA user mengisinya
        
        // Filter Tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $reqMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $reqSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();
            $query->whereBetween('tanggal_mulai', [$reqMulai, $reqSelesai]);
        }

        // Filter Kendaraan (berdasarkan NAMA)
        if ($request->filled('nama_kendaraan')) {
            // whereHas = Filter di tabel relasi (tabel kendaraan)
            $query->whereHas('kendaraan', function ($q) use ($request) {
                $q->where('nama_kendaraan', 'like', '%' . $request->nama_kendaraan . '%');
            });
        }

        // Filter Nopol
        if ($request->filled('nopol')) {
            // Ganti 'nopol' dengan 'nomor_polisi' jika itu nama kolom Anda
            $query->whereHas('kendaraan', function ($q) use ($request) {
                $q->where('nopol', 'like', '%' . $request->nopol . '%'); 
            });
        }

        // 4. Eksekusi kueri dengan paginate(10)
        // appends($filters) SANGAT PENTING agar filter tetap aktif saat pindah halaman
        $historyBookings = $query->paginate(10)->appends($filters);

        // 5. Kirim data ke view
        return view('admin.booking.history', compact('historyBookings'));
    }

    public function exportHistory(Request $request)
    {
        // 1. Ambil semua filter dari URL
        $filters = $request->query();

        // 2. Tentukan nama file
        $fileName = 'riwayat_booking_' . Carbon::now()->format('d-m-Y') . '.xlsx';

        // 3. Panggil Export Class dan kirimkan filter, lalu download
        return Excel::download(new BookingsHistoryExport($filters), $fileName);
    }
}
