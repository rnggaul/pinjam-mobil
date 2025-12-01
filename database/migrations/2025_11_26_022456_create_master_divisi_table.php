<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_divisi', function (Blueprint $table) {
            $table->integer('id_divisi', true);
            $table->string('nama_divisi', 35)->nullable();
        });

        DB::table('master_divisi')->insert([
            'nama_divisi' => 'IT',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_divisi');
    }
};
