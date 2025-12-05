<?php

namespace App\Console\Commands;
use App\Models\Booking;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ExpireOverdueBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis set status expired untuk booking pending yang lewat tanggal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $count = Booking::where('status', 'pending')
            ->whereDate('tanggal_mulai', '<', $today)
            ->update(['status' => 'expired']);

            
    $this->info("Berhasil mengubah {$count} status booking menjadi expired.");
    }

}
