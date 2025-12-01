<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Booking') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Pesan Sukses --}}
            @if (session('success1'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success1') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-semibold mb-4">Daftar Booking (Pending)</h3>

                    <a href="{{ route('admin.booking.history') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                        Lihat Riwayat (Selesai/Ditolak)
                    </a>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departemen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kendaraan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Polisi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tujuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keperluan</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pendingBookings as $booking)
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
                                        {{ $booking->kendaraan->nopol ?? 'Kendaraan Dihapus' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $booking->tanggal_mulai->format('d M Y') }} - {{ $booking->tanggal_selesai->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $booking->tujuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $booking->keperluan }}
                                    </td>

                                    {{--GANTI SELURUH ISI 'td' LAMA ANDA DENGAN BLOK KODE BARU INI. 'td' ini sekarang berisi 2 modal: 'Approve' dan 'Reject'. --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">

                                        {{-- ================================================= --}}
                                        {{-- 1. MODAL UNTUK "APPROVE" (BARU) --}}
                                        {{-- ================================================= --}}
                                        <div x-data="{ open: false }">
                                            {{-- Tombol Pemicu (Trigger) --}}
                                            <button @click="open = true" type="button" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                                Approve
                                            </button>

                                            {{-- Modal/Popup --}}
                                            <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
                                                {{-- Latar Belakang Overlay --}}
                                                <div @click="open = false" class="fixed inset-0 bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                                                {{-- Konten Modal --}}
                                                <div @click.away="open = false" class="relative z-10 w-full max-w-lg p-6 bg-white rounded-lg shadow-xl">

                                                    <form action="{{ route('admin.booking.updateStatus', $booking) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="approved">

                                                        <h3 class="text-lg font-medium text-gray-900 text-left">
                                                            Setujui Booking #{{ $booking->booking_id }}
                                                        </h3>

                                                        {{-- Tampilkan Info Driver --}}
                                                        <div class="mt-4 text-left">
                                                            <p class="text-sm text-gray-600 mb-2">
                                                                User meminta supir:
                                                                <span class="font-bold {{ $booking->pakai_driver == 'ya' ? 'text-blue-600' : 'text-gray-500' }}">
                                                                    {{ ucfirst($booking->pakai_driver) }}
                                                                </span>
                                                            </p>

                                                            {{-- LOGIKA: Tampilkan Dropdown HANYA JIKA user minta supir --}}
                                                            @if($booking->pakai_driver == 'ya')
                                                            <label for="driver_id_{{ $booking->booking_id }}" class="block text-sm font-medium text-gray-700">
                                                                Pilih Driver yang Tersedia (Wajib)
                                                            </label>

                                                            <select
                                                                id="driver_id_{{ $booking->booking_id }}"
                                                                name="driver_id"
                                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"
                                                                required>

                                                                <option value="">-- Pilih Driver --</option>

                                                                @foreach ($drivers as $driver)
                                                                @php
                                                                // 1. FORMAT TANGGAL SAJA (Hapus Jam/Menit/Detik)
                                                                $reqStart = \Carbon\Carbon::parse($booking->tanggal_mulai)->format('Y-m-d');
                                                                $reqEnd = \Carbon\Carbon::parse($booking->tanggal_selesai)->format('Y-m-d');

                                                                $isBusy = $busySchedules->contains(function ($schedule) use ($driver, $reqStart, $reqEnd, $booking) {

                                                                // 2. CEK ID DRIVER (Perhatikan ini!)
                                                                // Pastikan Anda pakai nama kolom yang BENAR dari tabel Driver.
                                                                // Biasanya 'id' atau 'id_driver'. Sesuaikan dengan database Anda!
                                                                // Di sini saya pakai $driver->id_driver (asumsi PK tabel driver adalah id_driver)

                                                                // Cek properti mana yang ada isinya:
                                                                $driverId = $driver->id_driver ?? $driver->driver_id ?? $driver->id;

                                                                if ($schedule->driver_id != $driverId) {
                                                                return false; // Kalau orangnya beda, jelas tidak bentrok
                                                                }

                                                                // 3. ABAIKAN BOOKING DIRI SENDIRI (Kalau lagi edit)
                                                                if ($schedule->booking_id == $booking->booking_id) {
                                                                return false;
                                                                }

                                                                // 4. CEK STATUS (Abaikan yang rejected/cancelled)
                                                                if (in_array($schedule->status, ['rejected', 'cancelled'])) {
                                                                return false;
                                                                }

                                                                // 5. RUMUS BENTROK TANGGAL (Tanpa Jam)
                                                                $schStart = \Carbon\Carbon::parse($schedule->tanggal_mulai)->format('Y-m-d');
                                                                $schEnd = \Carbon\Carbon::parse($schedule->tanggal_selesai)->format('Y-m-d');

                                                                // Logika: (Mulai A <= Selesai B) DAN (Selesai A>= Mulai B)
                                                                    return $schStart <= $reqEnd && $schEnd>= $reqStart;
                                                                        });
                                                                        @endphp

                                                                        {{-- Tampilkan hanya jika TIDAK sibuk --}}
                                                                        @if (!$isBusy)
                                                                        {{-- Pastikan value ini sesuai PK tabel driver (id_driver atau driver_id) --}}
                                                                        <option value="{{ $driver->id_driver ?? $driver->driver_id }}">
                                                                            {{ $driver->nama_driver }}
                                                                        </option>
                                                                        @endif
                                                                        @endforeach

                                                            </select>

                                                            {{-- Pesan kecil jika tidak ada driver muncul --}}
                                                            <p class="text-xs text-gray-500 mt-1">
                                                                * Driver yang sedang bertugas di tanggal ini ({{ $booking->tanggal_mulai->format('d/m') }} - {{ $booking->tanggal_selesai->format('d/m') }}) tidak akan muncul.
                                                            </p>

                                                            <x-input-error :messages="$errors->get('driver_id')" class="mt-2" />

                                                            @else
                                                            {{-- Jika TIDAK butuh supir --}}
                                                            <div class="p-2 bg-green-50 border border-green-200 rounded text-sm text-green-700">
                                                                Unit ini dipinjam tanpa supir (Lepas Kunci).
                                                            </div>
                                                            @endif
                                                        </div>

                                                        <div class="mt-6 flex justify-end space-x-3">
                                                            <button @click="open = false" type="button" type="button" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                                                Batal
                                                            </button>
                                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                                                Konfirmasi Persetujuan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                        {{-- ================================================= --}}
                                        {{-- 2. MODAL UNTUK "REJECT" (Kode Anda sebelumnya) --}}
                                        {{-- ================================================= --}}
                                        <div x-data="{ open: false }">
                                            {{-- Tombol Pemicu (Trigger) --}}
                                            <button @click="open = true" type="button" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                                                Reject
                                            </button>

                                            {{-- Modal/Popup --}}
                                            <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
                                                <div @click="open = false" class="fixed inset-0 bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                                                {{-- Konten Modal --}}
                                                <div @click.away="open = false" class="relative z-10 w-full max-w-lg p-6 bg-white rounded-lg shadow-xl">
                                                    <form action="{{ route('admin.booking.updateStatus', $booking) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="rejected">
                                                        <h3 class="text-lg font-medium text-gray-900 text-left">
                                                            Tolak Booking #{{ $booking->booking_id }}
                                                        </h3>
                                                        <div class="mt-4 text-left">
                                                            <label for="note_{{ $booking->booking_id }}" class="block text-sm font-medium text-gray-700">
                                                                Alasan Penolakan (Wajib)
                                                            </label>
                                                            <textarea id="note_{{ $booking->booking_id }}" name="note" class="mt-1 block w-full rounded-md ..." rows="3" required>{{ old('note') }}</textarea>
                                                            <x-input-error :messages="$errors->get('note')" class="mt-2" />
                                                        </div>
                                                        <div class="mt-6 flex justify-end space-x-3">
                                                            <button @click="open = false" type="button" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                                                Batal
                                                            </button>
                                                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                                Konfirmasi Penolakan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-5 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Tidak ada request booking yang pending.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $pendingBookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>