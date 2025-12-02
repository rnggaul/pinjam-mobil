<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Kode ini sudah benar
        $divisis = \App\Models\Divisi::paginate(10);
        return view('divisi.masterDivisi', compact('divisis'));
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'nama_divisi' => 'required|string|max:255|unique:master_divisi,nama_divisi',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Simpan
        Divisi::create([
            'nama_divisi' => $request->nama_divisi
        ]);

        // 3. Return JSON Sukses
        return response()->json([
            'success' => true,
            'message' => 'Divisi berhasil ditambahkan!'
        ]);
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
        // 1. Validasi (Abaikan ID diri sendiri agar tidak error "Unique")
        $validator = Validator::make($request->all(), [
            'nama_divisi' => 'required|string|max:255|unique:master_divisi,nama_divisi,' . $divisi->id_divisi . ',id_divisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Update Data
        $divisi->update([
            'nama_divisi' => $request->nama_divisi
        ]);

        // 3. Return JSON
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui!'
        ]);
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
