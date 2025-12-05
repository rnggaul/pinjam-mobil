<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Mail\BookingAdminAlert;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotifyAdminsOfUpcomingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:notify-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email rekap ke admin untuk booking H-3';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. tentukan rentang waktu hari ini sampai h+3
        $tanggal_mulai = Carbon::today();
        $tanggal_selesai = Carbon::today()->addDays(3);

        // 2. ambil booking yang tanggal_mulai antara rentang waktu tersebut dan statusnya 'pending'
        $bookings =Booking::where('status', 'pending')
            ->whereBetween('tanggal_mulai', [$tanggal_mulai, $tanggal_selesai])
            ->get();

        // 3. cek apakah ada booking yang ditemukan
        if ($bookings->isEmpty()) {
            $this->info('Tidak ada booking yang akan dimulai dalam 3 hari ke depan.');
            return; 
        }

        // 4. ambil email admin
        $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();

        if(empty($adminEmails)){
            $this->error('Tidak ada admin yang ditemukan untuk dikirimi email.');
            return;
        }

        // 5. kirim email ke admin
        Mail::to($adminEmails)->send(new BookingAdminAlert($bookings));

        $this->info('Email notifikasi telah dikirim ke admin untuk '. $bookings->count(). ' booking');

    }
}
