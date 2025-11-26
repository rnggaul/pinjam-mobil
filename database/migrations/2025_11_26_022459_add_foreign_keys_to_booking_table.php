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
        Schema::table('booking', function (Blueprint $table) {
            $table->foreign(['mobil_id'], 'booking_ibfk_2')->references(['mobil_id'])->on('master_kendaraan')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['user_id'], 'booking_ibfk_3')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['driver_id'], 'booking_ibfk_4')->references(['driver_id'])->on('master_driver')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropForeign('booking_ibfk_2');
            $table->dropForeign('booking_ibfk_3');
            $table->dropForeign('booking_ibfk_4');
        });
    }
};
