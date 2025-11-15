<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Security - Serah Terima Kendaraan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Pesan Sukses/Error --}}
            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- FORM FILTER (Sama seperti filter admin) --}}
            <div class="mb-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Filter Booking</h3>
                    <form action="{{ route('security.dashboard') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-5">
                            <div>
                                <label for="nama_kendaraan" class="block font-medium text-sm text-gray-700">Nama Peminjam</label>
                                <input type="text" name="name" id="name" value="{{ request('name') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" placeholder="Cari Bayu">
                            </div>
                            <div>
                                <label for="nama_kendaraan" class="block font-medium text-sm text-gray-700">Nama Kendaraan</label>
                                <input type="text" name="nama_kendaraan" id="nama_kendaraan" value="{{ request('nama_kendaraan') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" placeholder="Cari Avanza...">
                            </div>
                            <div>
                                <label for="nopol" class="block font-medium text-sm text-gray-700">Nomor Polisi</label>
                                <input type="text" name="nopol" id="nopol" value="{{ request('nopol') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" placeholder="Cari B 1234...">
                            </div>
                            <div>
                                <label for="tanggal_mulai" class_mulai" class="block font-medium text-sm text-gray-700">Dari Tanggal</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                            </div>
                            <div>
                                <label for="tanggal_selesai" class_selesai" class="block font-medium text-sm text-gray-700">Sampai Tanggal</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4 space-x-2">
                            <a href="{{ route('security.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">Reset</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- DAFTAR BOOKING (Mirip history.blade.php) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Daftar Serah Terima</h3>
                    
                    <div class="space-y-6">
                        @forelse ($bookings as $booking)
                            <div class="border rounded-lg p-4 shadow-sm">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    
                                    {{-- Info Kendaraan & User --}}
                                    <div class="md:col-span-1">
                                        <h3 class="font-bold text-lg">{{ $booking->kendaraan->nama_kendaraan ?? 'N/A' }}</h3>
                                        <p class="text-sm text-gray-600">{{ $booking->kendaraan->nopol ?? 'N/A' }}</p>
                                        <hr class="my-2">
                                        <p class="text-sm font-semibold">Peminjam:</p>
                                        <p class="text-sm">{{ $booking->user->name ?? 'N/A' }}</p>
                                        <p class="text-sm">{{ $booking->user?->divisi?->nama_divisi ?? 'N/A' }}</p>
                                    </div>

                                    {{-- Info & Aksi Booking --}}
                                    <div class="md:col-span-2">
                                        <div class="flex justify-between items-start">
                                            <p class="text-sm mt-2">
                                                <strong>Dipesan:</strong> {{ $booking->tanggal_mulai->format('d M Y') }} 
                                                <strong>s/d</strong> {{ $booking->tanggal_selesai->format('d M Y') }}
                                            </p>
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                @if($booking->status == 'berjalan') bg-yellow-100 text-yellow-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>

                                        {{-- LOGIKA KONDISIONAL UNTUK AKSI SECURITY --}}
                                        
                                        {{-- 1. JIKA STATUS 'approved' (Menunggu KM Awal) --}}
                                        @if ($booking->status == 'approved')
                                            <form action="{{ route('security.start', $booking) }}" method="POST" class="mt-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
                                                @csrf
                                                <label for="km_awal_{{ $booking->booking_id }}" class="block font-medium text-sm text-gray-700">Input KM Awal (Odometer)</label>
                                                <input type="number" name="km_awal" id="km_awal_{{ $booking->booking_id }}" step="0.1" min="0" class="block mt-1 w-full ..." placeholder="Contoh: 50250.5" required>
                                                <x-input-error :messages="$errors->get('km_awal')" class="mt-2" />
                                                <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                                    Mulai Perjalanan
                                                </button>
                                            </form>
                                        
                                        {{-- 2. JIKA STATUS 'berjalan' (Menunggu KM Akhir) --}}
                                        @elseif ($booking->status == 'berjalan')
                                            <p class="text-sm mt-2">
                                                <strong>KM Awal:</strong> {{ number_format($booking->km_awal, 1, '.', '') }} KM
                                            </p>
                                            <form action="{{ route('security.finish', $booking) }}" method="POST" class="mt-4 bg-green-50 p-4 rounded-lg border border-green-200">
                                                @csrf
                                                <label for="km_akhir_{{ $booking->booking_id }}" class="block font-medium text-sm text-gray-700">Input KM Akhir (Odometer)</label>
                                                <input type="number" name="km_akhir" id="km_akhir_{{ $booking->booking_id }}" step="0.1" min="{{ $booking->km_awal }}" class="block mt-1 w-full ..." placeholder="Contoh: {{ $booking->km_awal + 100 }}" required>
                                                <x-input-error :messages="$errors->get('km_akhir')" class="mt-2" />
                                                <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                                    Selesaikan Perjalanan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>Tidak ada booking yang perlu diproses (status 'approved' atau 'berjalan').</p>
                        @endforelse
                    </div>

                    {{-- LINK PAGINASI --}}
                    <div class="mt-6">
                        {{ $bookings->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>