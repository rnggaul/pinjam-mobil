<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();
        return view('driver.masterDriver', compact('drivers'));
    }

    public function create()
    {
        return view('driver.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_driver' => 'required|string|max:255',
        ]);

        Driver::create($request->all());

        return redirect()->route('driver.index')->with('success', 'Driver berhasil ditambahkan.');
    }

    public function edit(Driver $driver)
    {
        return view('driver.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'nama_driver' => 'required|string|max:255',
        ]);

        $driver->update($request->all());

        return redirect()->route('driver.index')->with('success', 'Data driver diperbarui.');
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