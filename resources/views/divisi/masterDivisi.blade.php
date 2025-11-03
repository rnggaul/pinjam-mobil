{{-- Asumsikan Anda punya layout utama di layouts/app.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Bungkus teks dengan tag <a> (link). href mengarah ke route 'admin.dashboard' 'hover:underline' memberikan efek garis bawah saat mouse diarahkan --}}
            <a href="{{ route('admin.index') }}" class="hover:underline">
                {{ __('Manajemen Divisi') }}
            </a>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- TOMBOL TAMBAH DATA --}}
                    <a href="{{ route('divisi.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mb-4">
                        Tambah Divisi Baru
                    </a>

                    {{-- Menampilkan pesan sukses --}}
                    @if (session('success'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    {{-- Menampilkan pesan error (jika gagal hapus) --}}
                    @if (session('error'))
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif

                    {{-- TABEL DATA --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Divisi</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($divisis as $divisi)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $divisi->id_divisi }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $divisi->nama_divisi }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('divisi.edit', $divisi) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>

                                    {{-- Tombol Hapus (dalam form) --}}
                                    <form action="{{ route('divisi.destroy', $divisi) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 ml-4">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            {{-- Tampilan jika data kosong --}}
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data divisi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>