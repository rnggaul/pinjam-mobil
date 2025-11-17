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
        Schema::create('booking', function (Blueprint $table) {
            $table->integer('booking_id', true);
            $table->integer('mobil_id')->index('mobil_id');
            $table->integer('user_id')->index('user_id');
            $table->decimal('km_awal', 10, 0)->nullable();
            $table->decimal('km_akhir', 10, 0)->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('note')->nullable();
            $table->text('driver')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'finish','running']);
            $table->datetime('jam_keluar')->nullable();
            $table->datetime('jam_masuk')->nullable();
            $table->text('tujuan')->nullable();
            $table->text('keperluan')->nullable();

            $table->index(['user_id'], 'user_id_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
