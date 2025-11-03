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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-lg font-semibold mb-4">Daftar Booking (Approved, Finish, Rejected)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kendaraan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
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
                                            {{ $booking->kendaraan->nama_kendaraan ?? 'Kendaraan Dihapus' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $booking->tanggal_mulai->format('d M Y') }} - {{ $booking->tanggal_selesai->format('d M Y') }}
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
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Tidak ada riwayat booking.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>