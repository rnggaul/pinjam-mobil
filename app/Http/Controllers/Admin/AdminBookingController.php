<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class AdminBookingController extends Controller
{
    public function index()
    {
        // mengambil semua booking dengan status 'pending' lalu mengirimkan ke view
        $pendingBookings = Booking::where('status', 'pending')
                                    ->with(['user', 'kendaraan'])
                                    ->latest('tanggal_mulai')
                                    ->get();

        return view('admin.booking.index', compact('pendingBookings'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        // 1 validasi input dari form
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        // 2 update status booking
        $booking->update([
            'status' => $request->status
        ]);

        // 3 redirect kembali dengan pesan sukses
        return redirect()->route('admin.booking.index')->with('success', 'Status booking berhasil diperbarui.');
    }

    public function history()
    {
        $historyBookings = Booking::where('status', '!=', 'pending')
                                    ->with(['user', 'kendaraan'])
                                    ->latest('tanggal_mulai')
                                    ->get();

        return view('admin.booking.history', compact('historyBookings'));
    }
}
