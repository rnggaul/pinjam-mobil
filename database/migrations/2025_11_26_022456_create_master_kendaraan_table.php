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
            $table->integer('mobil_id', true);
            $table->string('nama_kendaraan', 30);
            $table->string('nopol', 11);
            $table->enum('jenis_mobil', ['Sedan', 'LCGC', 'SUV', 'MPV']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kendaraan');
    }
};
