<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    // 1 menentukan nama tabel
    protected $table = 'master_kendaraan';
    
    // 2 menentukan primary key
    protected $primaryKey = 'mobil_id';

    // 3 matikan timestamps
    public $timestamps = false;

    // 4 menentukan kolom yang boleh diisi
    protected $fillable = [
        'nama_kendaraan',
        'nopol',
        'jenis_mobil',
        'KM',
    ];

    public function booking()
    {
        return $this->hasMany(Booking::class, 'mobil_id', 'mobil_id');
    }
}
