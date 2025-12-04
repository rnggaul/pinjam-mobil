<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $kendaraans = Kendaraan::paginate(10);
        return view('kendaraan.masterKendaraan', compact('kendaraans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('kendaraan.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kendaraan' => 'required|string|max:30',
            'nopol' => [
                'unique:master_kendaraan',
                'required',
                'string',
                'max:11',
                'regex:/^[A-Z]{1,2}\s[0-9]{1,4}\s[A-Z]{1,3}$/',
            ],
            'jenis_mobil' => 'required|in:Sedan,LCGC,SUV,MPV',
        ], [[
            'nopol.regex' => 'Nomor polisi tidak valid. Format yang benar: "AB 1234 CD".',
            'nopol.unique' => 'Nomor polisi sudah terdaftar.',
        ]]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        Kendaraan::create($request->all());

        return response()->json(['success' => true, 'message' => 'Kendaraan berhasil ditambahkan!']);
    }
    // public function store(Request $request)
    // {
    //     // 1 validasi
    //     $request->validate([
    //         'nama_kendaraan' => 'required|string|max:255',
    //         'nopol' => [
    //             'unique:master_kendaraan',
    //             'required',
    //             'string',
    //             'max:11',
    //             'regex:/^[A-Z]{1,2}\s[0-9]{1,4}\s[A-Z]{1,3}$/',
    //         ],
    //         'jenis_mobil' => 'required|in:Sedan,LCGC,SUV,MPV',
    //     ], [
    //         'nopol.regex' => 'Nomor polisi tidak valid. Format yang benar: "AB 1234 CD".',
    //         'nopol.unique' => 'Nomor polisi sudah terdaftar.',
    //     ]);
    //     // 2 buat data baru
    //     Kendaraan::create($request->all());

    //     // 3 redirect
    //     return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan. ');
    // }

    /**
     * Display the specified resource.
     */
    public function show(Kendaraan $kendaraan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kendaraan $kendaraan)
    {
        //
        return view('kendaraan.edit', compact('kendaraan'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Kendaraan $kendaraan)
    // {
    //     // 1 validasi
    //     $request->validate([
    //         'nama_kendaraan' => 'required|string|max:255',
    //         'jenis_mobil' => 'required|in:Sedan,LCGC,SUV,MPV',
    //     ]);

    //     // 2 buat data baru
    //     //Kendaraan::create($request->all());
    //     $kendaraan->update($request->all());

    //     // 3 redirect
    //     return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil diubah. ');
    // }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $validator = Validator::make($request->all(), [
            'nama_kendaraan' => 'required|string|max:30',
            'jenis_mobil' => 'required|in:Sedan,LCGC,SUV,MPV',
            'nopol' => [
                'unique:master_kendaraan',
                'required',
                'string',
                'max:11',
                'regex:/^[A-Z]{1,2}\s[0-9]{1,4}\s[A-Z]{1,3}$/',
            ],
        ],[
            'nopol.regex' => 'Nomor polisi tidak valid. Format yang benar: "AB 1234 CD".',
            'nopol.unique' => 'Nomor polisi sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $kendaraan->update($request->all());

        return response()->json(['success' => true, 'message' => 'Data kendaraan berhasil diperbarui!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kendaraan $kendaraan)
    {
        //
        try {
            $kendaraan->delete();
            return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('kendaraan.index')->with('error', 'Gagal menghapus kendaraan. Mungkin kendaraan ini sedang digunakan dalam pemesanan.');
        }
    }
}
