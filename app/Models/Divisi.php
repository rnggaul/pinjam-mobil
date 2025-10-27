<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    /**
     * memberi tau laravel nama tabel yang benar di database
     */
    protected $table = 'master_divisi';

    /**
     * menentukan kolom mana yang boleh diisi
     */
    protected $fillable = ['nama_divisi'];

    protected $primaryKey = 'id_divisi';

    public $timestamps = false;
}
