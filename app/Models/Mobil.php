<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    use HasFactory;

    protected $table = 'mobil';

    protected $primaryKey = 'mobil_id';

    public function booking()
    {
        return $this->hasMany(Booking::class, 'mobil_id', 'mobil_id');
    }
}
