<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_kendaraan', function (Blueprint $table) {
            $table->id('mobil_id');
            $table->string('nama_mobil');
            $table->string('nopol')->unique();
            $table->enum('jenis_mobil', ['Sedan', 'LCGC', 'SUV', 'MPV',]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
