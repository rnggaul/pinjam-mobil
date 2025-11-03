<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Peminjaman Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Menampilkan Pesan Sukses (dari redirect) --}}
            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Menampilkan Pesan Error (dari redirect) --}}
            @if (session('error'))
                <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-lg font-semibold mb-4">Daftar Booking Anda</h3>
                    
                    <div class="space-y-6">
                        @forelse ($bookings as $booking)
                            {{-- Card untuk setiap booking --}}
                            <div class="border rounded-lg p-4 shadow-sm">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    
                                    {{-- Kolom 1: Info Kendaraan --}}
                                    <div class="md:col-span-1">
                                        @if($booking->kendaraan)
                                            <h3 class="font-bold text-lg mt-2">{{ $booking->kendaraan->nama_kendaraan }}</h3>
                                            <p class="text-sm text-gray-600">{{ $booking->kendaraan->nomor_polisi }}</p>
                                        @else
                                            <p class="text-red-500">Kendaraan telah dihapus.</p>
                                        @endif
                                    </div>

                                    {{-- Kolom 2: Info & Aksi Booking --}}
                                    <div class="md:col-span-2">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-semibold">Booking ID: #{{ $booking->booking_id }}</h4>
                                            
                                            {{-- Status Badge --}}
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                @if($booking->status == 'finish') bg-green-100 text-green-800
                                                @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status == 'approved') bg-blue-100 text-blue-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>

                                        <p class="text-sm mt-2">
                                            <strong>Dipesan:</strong> {{ $booking->tanggal_mulai->format('d M Y') }} 
                                            <strong>s/d</strong> {{ $booking->tanggal_selesai->format('d M Y') }}
                                        </p>

                                        {{-- ================================================= --}}
                                        {{-- LOGIKA KONDISIONAL UNTUK AKSI --}}
                                        {{-- ================================================= --}}

                                        {{-- 1. JIKA STATUS 'approved' (Disetujui Admin) --}}
                                        @if ($booking->status == 'approved')

                                            {{-- 1A. JIKA KM Awal BELUM diisi --}}
                                            @if (is_null($booking->km_awal))
                                                <form action="{{ route('booking.start', $booking) }}" method="POST" class="mt-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
                                                    @csrf
                                                    <label for="km_awal_{{ $booking->booking_id }}" class="block font-medium text-sm text-gray-700">Input KM Awal (Odometer)</label>
                                                    <p class="text-xs text-gray-500 mb-2">Lihat odometer mobil dan masukkan KM saat ini untuk memulai.</p>
                                                    <input type="decimal" 
                                                           name="km_awal" 
                                                           id="km_awal_{{ $booking->booking_id }}" 
                                                           step="0.1" 
                                                           min="0" 
                                                           class="block mt-1 w-full rounded-md shadow-sm border-gray-300" 
                                                           placeholder="Contoh: 50250.5" 
                                                           required>
                                                    <x-input-error :messages="$errors->get('km_awal')" class="mt-2" />
                                                    <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                                        Mulai Peminjaman
                                                    </button>
                                                </form>
                                            
                                            {{-- 1B. JIKA KM Awal SUDAH diisi (Menunggu KM Akhir) --}}
                                            @else
                                                <p class="text-sm mt-2">
                                                    <strong>KM Awal:</strong> {{ $booking->km_awal }} KM
                                                </p>
                                                <form action="{{ route('booking.finish', $booking) }}" method="POST" class="mt-4 bg-green-50 p-4 rounded-lg border border-green-200">
                                                    @csrf
                                                    <label for="km_akhir_{{ $booking->booking_id }}" class="block font-medium text-sm text-gray-700">Input KM Akhir (Odometer)</U></label>
                                                    <p class="text-xs text-gray-500 mb-2">Masukkan KM saat ini untuk menyelesaikan peminjaman.</p>
                                                    <input type="decimal" 
                                                           name="km_akhir" 
                                                           id="km_akhir_{{ $booking->booking_id }}" 
                                                           step="0.1" 
                                                           min="{{ $booking->km_awal }}" 
                                                           class="block mt-1 w-full rounded-md shadow-sm border-gray-300" 
                                                           placeholder="Contoh: {{ $booking->km_awal + 100 }}" 
                                                           required>
                                                    <x-input-error :messages="$errors->get('km_akhir')" class="mt-2" />
                                                    <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                                        Selesaikan Peminjaman
                                                    </button>
                                                </form>
                                            @endif

                                        {{-- 2. JIKA STATUS 'finish' (Selesai) --}}
                                        @elseif ($booking->status == 'finish')
                                            <p class="text-sm mt-2">
                                                <strong>KM Awal:</strong> {{ $booking->km_awal }} KM
                                            </p>
                                            <p class="text-sm">
                                                <strong>KM Akhir:</strong> {{ $booking->km_akhir }} KM
                                            </p>
                                            <p class="text-sm font-semibold text-green-700">
                                                Total Pemakaian: {{ $booking->km_akhir - $booking->km_awal }} KM
                                            </p>
                                        
                                        {{-- 3. JIKA STATUS 'pending' (Menunggu) --}}
                                        @elseif ($booking->status == 'pending')
                                            <p class="text-sm text-yellow-700 mt-4">Menunggu persetujuan dari Admin.</p>
                                        
                                        {{-- 4. JIKA STATUS LAIN (misal: 'rejected') --}}
                                        @else
                                            <p class="text-sm text-red-700 mt-4">Booking ini ditolak atau dibatalkan.</p>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>Anda belum memiliki riwayat peminjaman.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>