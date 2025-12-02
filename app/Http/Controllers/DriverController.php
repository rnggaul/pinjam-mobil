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
        $validator = Validator::make($request->all(), [
            'nama_driver' => 'required|string|max:25|unique:master_driver,nama_driver',
            // Tambahkan validasi lain jika ada kolom no_hp, sim, dll
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        Driver::create($request->all());

        return response()->json(['success' => true, 'message' => 'Driver berhasil ditambahkan!']);
    }

    public function edit(Driver $driver)
    {
        return view('driver.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validator = Validator::make($request->all(), [
            'nama_driver' => 'required|string|max:25|unique:master_driver,nama_driver',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $driver->update($request->all());

        return response()->json(['success' => true, 'message' => 'Data driver berhasil diperbarui!']);
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
