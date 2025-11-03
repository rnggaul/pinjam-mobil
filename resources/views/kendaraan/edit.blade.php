{{-- resources/views/kendaraan/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kendaraan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                    <div class="mb-4">
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- 1. Ganti ACTION ke 'kendaraan.update' dan kirim $kendaraan --}}
                    <form action="{{ route('kendaraan.update', $kendaraan) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- 2. Tambahkan method PUT --}}

                        {{-- Input Nama Kendaraan --}}
                        <div>
                            <x-input-label for="nama_kendaraan" :value="__('Nama Kendaraan')" />
                            <input type="text"
                                name="nama_kendaraan"
                                id="nama_kendaraan"
                                value="{{ old('nama_kendaraan', $kendaraan->nama_kendaraan) }}"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                required
                                autofocus>
                            <x-input-error :messages="$errors->get('nama_kendaraan')" class="mt-2" />
                        </div>

                        <!-- {{-- Input Nomor Polisi --}}
                        <div class="mt-4">
                            <x-input-label for="nopol" :value="__('Nomor Polisi')" />
                            <input type="text"
                                name="nopol"
                                id="nopol"
                                value="{{ old('nopol', $kendaraan->nopol) }}"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                required>
                            <x-input-error :messages="$errors->get('nopol')" class="mt-2" />
                        </div> -->

                        {{-- Input Jenis Kendaraan (Dropdown) --}}
                        <div class="mt-4">
                            <x-input-label for="jenis_mobil" :value="__('Jenis Mobil')" />
                            <select id="jenis_mobil" name="jenis_mobil" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>

                                <option value="">Pilih Jenis Kendaraan</option>

                                {{-- Opsi dari Enum Anda --}}
                                <option value="Sedan" {{ old('jenis_mobil') == 'Sedan' ? 'selected' : '' }}>
                                    Sedan
                                </option>
                                <option value="LCGC" {{ old('jenis_mobil') == 'LCGC' ? 'selected' : '' }}>
                                    LCGC
                                </option>
                                <option value="SUV" {{ old('jenis_mobil') == 'SUV' ? 'selected' : '' }}>
                                    SUV
                                </option>
                                <option value="MPV" {{ old('jenis_mobil') == 'MPV' ? 'selected' : '' }}>
                                    MPV
                                </option>

                            </select>
                            <x-input-error :messages="$errors->get('jenis_kendaraan')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('kendaraan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>

                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Perbarui
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>