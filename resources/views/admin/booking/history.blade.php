<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Booking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <a href="{{ route('admin.booking.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali ke Request Booking</a>
            </div>

            {{-- ================================================= --}}
            {{-- BAGIAN BARU: FORM FILTER --}}
            {{-- ================================================= --}}
            <div class="mb-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Filter Riwayat</h3>
                    {{-- Form menggunakan method GET agar filter menempel di URL --}}
                    <form action="{{ route('admin.booking.history') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            
                            <div>
                                <label for="nama_kendaraan" class="block font-medium text-sm text-gray-700">Nama Kendaraan</label>
                                <input type="text" name="nama_kendaraan" id="nama_kendaraan" value="{{ request('nama_kendaraan') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" placeholder="Cari Avanza...">
                            </div>
                            
                            <div>
                                <label for="nopol" class="block font-medium text-sm text-gray-700">Nomor Polisi</label>
                                <input type="text" name="nopol" id="nopol" value="{{ request('nopol') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" placeholder="Cari B 1234...">
                            </div>
                            
                            <div>
                                <label for="tanggal_mulai" class="block font-medium text-sm text-gray-700">Dari Tanggal</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                            </div>

                            <div>
                                <label for="tanggal_selesai" class="block font-medium text-sm text-gray-700">Sampai Tanggal</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                            </div>

                        </div>
                        <div class="flex items-center justify-end mt-4 space-x-2">
                            <a href="{{ route('admin.booking.history') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                                Reset
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Filter
                            </button>
                            {{-- 
                              Tombol ini adalah link ke rute 'export'.
                              'request()->query()' SANGAT PENTING untuk mengirim filter Anda.
                            --}}
                            <a href="{{ route('admin.booking.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                Export ke Excel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- TABEL HASIL (Kode Anda sebelumnya) --}}
            {{-- ================================================= --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-lg font-semibold mb-4">Daftar Booking (Approved, Finish, Rejected)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departemen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kendaraan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Polisi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">KM</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($historyBookings as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $booking->user->name ?? 'User Dihapus' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $booking->user?->divisi?->nama_divisi ?? 'N/A' }}
                                    </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $booking->kendaraan->nama_kendaraan ?? 'Kendaraan Dihapus' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{-- Ganti 'nopol' jika nama kolom Anda berbeda --}}
                                            {{ $booking->kendaraan->nopol ?? 'N/A' }} 
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $booking->tanggal_mulai->format('d M Y') }} - {{ $booking->tanggal_selesai->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $booking->km_awal }} KM - {{ $booking->km_akhir }} KM
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{-- Status Badge --}}
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                @if($booking->status == 'finish') bg-green-100 text-green-800
                                                @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status == 'approved') bg-blue-100 text-blue-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- PERBAIKAN: Colspan sekarang 5 --}}
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Tidak ada riwayat booking yang cocok dengan filter Anda.
                                        </td>
                                    </tr>
                                @endForelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- ================================================= --}}
                    {{-- PERBAIKAN: Link Paginasi dengan appends() --}}
                    {{-- ================================================= --}}
                    <div class="mt-6">
                        {{-- appends(request()->query()) PENTING agar filter tetap aktif --}}
                        {{ $historyBookings->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>