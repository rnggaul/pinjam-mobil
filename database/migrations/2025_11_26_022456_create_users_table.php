<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name',30);
            $table->string('email',30)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('id_divisi')->index('id_divisi');
            $table->string('password');
            $table->enum('role', ['user', 'admin', 'security', 'superAdmin'])->default('user');
            $table->rememberToken();
            $table->timestamps();
            $table->boolean('must_change_password')->default(true);
        });

        // Menambahkan user default 'superAdmin'
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => hash::make('SuperAdmin123!'),
            'created_at' => now(),
            'updated_at' => now(),
            'role' => 'superAdmin',
            'must_change_password' => false,
            'id_divisi' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
