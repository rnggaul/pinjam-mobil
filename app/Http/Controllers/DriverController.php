<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::paginate(10);
        return view('driver.masterDriver', compact('drivers'));
    }

    public function create()
    {
        return view('driver.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            // Pastikan nama tabel benar 'master_driver'
            'nama_driver' => 'required|string|max:255|unique:master_driver,nama_driver',
        ], [
            // Pesan Error Custom (Opsional)
            'nama_driver.required' => 'Nama driver wajib diisi.',
            'nama_driver.unique' => 'Nama driver sudah terdaftar.',
        ]);

        // 2. Cek jika gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. Simpan ke Database
        Driver::create([
            'nama_driver' => $request->nama_driver
        ]);

        // 4. Return JSON Sukses
        return response()->json([
            'success' => true,
            'message' => 'Driver baru berhasil ditambahkan!'
        ]);
    }

    public function edit(Driver $driver)
    {
        return view('driver.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            // PENTING: Tambahkan ID driver di parameter ke-3 unique rule
            // Format: unique:nama_tabel,nama_kolom,id_yang_diabaikan
            'nama_driver' => 'required|string|max:255|unique:master_driver,nama_driver,' . $driver->id,
        ], [
            'nama_driver.unique' => 'Nama driver sudah digunakan oleh driver lain.',
        ]);

        // 2. Cek jika gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. Update Data
        $driver->update([
            'nama_driver' => $request->nama_driver
        ]);

        // 4. Return JSON Sukses
        return response()->json([
            'success' => true,
            'message' => 'Data driver berhasil diperbarui!'
        ]);
    }

    public function destroy(Driver $driver)
    {
        try {
            $driver->delete();
            return redirect()->route('driver.index')->with('success', 'Driver dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('driver.index')->with('error', 'Gagal menghapus driver (mungkin sedang digunakan).');
        }
    }
}
