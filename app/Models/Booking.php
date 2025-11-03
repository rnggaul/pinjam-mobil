<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'booking';

    protected $primaryKey = 'booking_id';

    public $timestamps = false;


    protected $guarded = [];

    // protected $dates = [
    //     'tanggal_mulai',
    //     'tanggal_selesai'
    // ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'mobil_id', 'mobil_id');
    }
}
