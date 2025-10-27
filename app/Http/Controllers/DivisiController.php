<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Kode ini sudah benar
        $divisis = \App\Models\Divisi::all();
        return view('divisi.masterDivisi', compact('divisis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Kode ini sudah benar
        return view('divisi.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * PERBAIKAN ADA DI BAWAH INI
     */
    public function store(Request $request)
    {
        // 1. Validasi (diubah ke 'nama_divisi' dan ditambah 'unique')
        $request->validate([
            'nama_divisi' => 'required|string|max:255|unique:master_divisi,nama_divisi',
        ]);

        // 2. Buat data baru (Model 'Divisi' dengan 'D' besar)
        Divisi::create([
            'nama_divisi' => $request->nama_divisi, // (diambil dari $request->nama_divisi)
        ]);

        // 3. Redirect (Kode Anda sudah benar)
        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Divisi $divisi)
    {
        // Dibiarkan kosong tidak masalah jika tidak dipakai
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Divisi $divisi)
    {
        // Kode ini sudah benar
        return view('divisi.edit', compact('divisi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Divisi $divisi)
    {
        // Kode Anda di sini sudah benar
        // 1. Validasi
        $request->validate([
            'nama_divisi' => 'required|string|max:255|unique:master_divisi,nama_divisi,' . $divisi->id_divisi . ',id_divisi',
        ]);

        // 2. Update data
        $divisi->update([
            'nama_divisi' => $request->nama_divisi,
        ]);

        // 3. Redirect
        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Divisi $divisi)
    {
        // Kode Anda di sini sudah benar
        try {
            $divisi->delete();
            return redirect()->route('divisi.index')->with('success', 'Divisi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('divisi.index')->with('error', 'Gagal menghapus divisi. Mungkin divisi ini sedang digunakan.');
        }
    }
}
