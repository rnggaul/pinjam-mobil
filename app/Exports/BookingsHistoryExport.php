<?php

namespace App\Exports;

use App\Models\Booking;
use Illuminate\Http\Request; // <-- Tambahkan ini
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon; // <-- Tambahkan ini

class BookingsHistoryExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    /**
    * Kita gunakan __construct() untuk menerima filter dari controller
    */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
    * Kueri ini MENG-COPY-PASTE logika filter dari AdminBookingController
    */
    public function query()
    {
        $query = Booking::where('status', '!=', 'pending')
                            ->with(['user.divisi', 'kendaraan']) // Kita ambil relasi user, divisi, & kendaraan
                            ->latest('tanggal_mulai');

        // Terapkan filter Tanggal
        if (!empty($this->filters['tanggal_mulai']) && !empty($this->filters['tanggal_selesai'])) {
            $reqMulai = Carbon::parse($this->filters['tanggal_mulai'])->startOfDay();
            $reqSelesai = Carbon::parse($this->filters['tanggal_selesai'])->endOfDay();
            $query->whereBetween('tanggal_mulai', [$reqMulai, $reqSelesai]);
        }

        // Filter Nama Kendaraan
        if (!empty($this->filters['nama_kendaraan'])) {
            $query->whereHas('kendaraan', function ($q) {
                $q->where('nama_kendaraan', 'like', '%' . $this->filters['nama_kendaraan'] . '%');
            });
        }

        // Filter Nopol (sesuaikan nama kolom 'nopol')
        if (!empty($this->filters['nopol'])) {
            $query->whereHas('kendaraan', function ($q) {
                $q->where('nopol', 'like', '%' . $this->filters['nopol'] . '%');
            });
        }
        
        return $query;
    }

    /**
    * Ini adalah judul kolom di file Excel Anda
    */
    public function headings(): array
    {
        return [
            'Booking ID',
            'Peminjam',
            'Divisi',
            'Kendaraan',
            'Nopol',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status',
            'KM Awal',
            'KM Akhir',
            'Total KM',
        ];
    }

    /**
    * Ini adalah data untuk setiap baris di Excel
    */
    public function map($booking): array
    {
        $totalKm = ($booking->km_akhir && $booking->km_awal) ? $booking->km_akhir - $booking->km_awal : 0;
        
        return [
            $booking->booking_id,
            $booking->user->name ?? 'N/A',
            $booking->user?->divisi?->nama_divisi ?? 'N/A', // Ambil nama divisi
            $booking->kendaraan->nama_kendaraan ?? 'N/A',
            $booking->kendaraan->nopol ?? 'N/A', // (sesuaikan nama kolom 'nopol')
            $booking->tanggal_mulai->format('d-m-Y H:i'),
            $booking->tanggal_selesai->format('d-m-Y H:i'),
            ucfirst($booking->status),
            $booking->km_awal,
            $booking->km_akhir,
            $totalKm,
        ];
    }
}