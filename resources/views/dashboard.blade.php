<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cari Ketersediaan Kendaraan') }}
        </h2>
    </x-slot>

    {{-- 
      TAMBAHAN: Kita hitung tanggal hari ini dan H+7 di sini
      agar bisa dipakai di logika @if di bawah.
    --}}
    @php
        $today = now()->format('Y-m-d');
        $maxDate = now()->addDays(7)->format('Y-m-d');
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 1. FORM FILTER TANGGAL (Tidak berubah) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('dashboard') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="tanggal_mulai" :value="__('Dari Tanggal')" />
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                       value="{{ request('tanggal_mulai') }}"
                                       class="block mt-1 w-full rounded-md shadow-sm border-gray-300" 
                                       required>
                                <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="tanggal_selesai" :value="__('Sampai Tanggal')" />
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                       value="{{ request('tanggal_selesai') }}"
                                       class="block mt-1 w-full rounded-md shadow-sm border-gray-300" 
                                       required>
                                <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase ...">
                                    Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- 2. AREA HASIL PENCARIAN (LOGIKA DIPERBARUI) --}}
            {{-- ================================================= --}}
            
            {{-- KONDISI 1: Tanggal Mulai ada di masa lalu --}}
            @if (request()->filled('tanggal_mulai') && request('tanggal_mulai') < $today)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 text-red-800 bg-red-100 border border-red-300 rounded-lg">
                        <h3 class="font-semibold text-lg">Input Tanggal Tidak Valid</h3>
                        <p class="mt-2">Tanggal Mulai tidak boleh di masa lalu. Silakan pilih hari ini atau ke depan.</p>
                    </div>
                </div>

            {{-- KONDISI 2: Tanggal Mulai > H+7 (Permintaan Anda) --}}
            @elseif (request()->filled('tanggal_mulai') && request('tanggal_mulai') > $maxDate)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 text-red-800 bg-red-100 border border-red-300 rounded-lg">
                        <h3 class="font-semibold text-lg">Input Tanggal Tidak Valid</h3>
                        <p class="mt-2">Anda hanya dapat mencari ketersediaan untuk 7 hari ke depan.</p>
                    </div>
                </div>

            {{-- KONDISI 3: Tanggal Selesai < Tanggal Mulai --}}
            @elseif (request()->filled('tanggal_mulai') && request()->filled('tanggal_selesai') && request('tanggal_selesai') < request('tanggal_mulai'))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 text-red-800 bg-red-100 border border-red-300 rounded-lg">
                        <h3 class="font-semibold text-lg">Input Tanggal Tidak Valid</h3>
                        <p class="mt-2">Tanggal Selesai tidak boleh lebih awal dari Tanggal Mulai.</p>
                    </div>
                </div>

            {{-- KONDISI 4: TANGGAL VALID (Tampilkan Hasil) --}}
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold mb-4 text-lg">Hasil Ketersediaan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @forelse ($kendaraans as $kendaraan)
                                <div class="border rounded-lg shadow-sm p-4 flex flex-col justify-between">
                                    <div>
                                        <h4 class="font-bold text-xl">{{ $kendaraan->nama_kendaraan }}</h4>
                                        <p class="text-sm text-gray-600">{{ $kendaraan->nopol }}</p>
                                        <p class="text-sm text-gray-800">{{ $kendaraan->jenis_mobil }}</p>
                                    </div>
                                    <form action="{{ route('booking.store') }}" method="POST" class="mt-4">
                                        @csrf
                                        <input type="hidden" name="mobil_id" value="{{ $kendaraan->mobil_id }}">
                                        <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                                        <input type="hidden" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500"> Booking </button>
                                    </form>
                                </div>
                            @empty
                                @if (request()->has('tanggal_mulai'))
                                    <p class="col-span-3 text-center text-gray-500">Tidak ada kendaraan yang tersedia pada rentang tanggal tersebut.</p>
                                @else
                                    <p class="col-span-3 text-center text-gray-500">Silakan pilih rentang tanggal untuk mencari kendaraan.</p>
                                @endif
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif {{-- Ini adalah penutup dari semua @if --}}

        </div>
    </div>
</x-app-layout>