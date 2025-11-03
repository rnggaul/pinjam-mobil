<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Kendaraan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Menampilkan error validasi --}}
                    @if ($errors->any())
                    <div class="mb-4">
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('kendaraan.store') }}" method="POST">
                        @csrf
                        <div>
                            <label for="nama_kendaraan" class="block font-medium text-sm text-gray-700">Nama Kendaraan</label>
                            <input type="text" name="nama_kendaraan" id="nama_kendaraan" value="{{ old('nama_Kendaraan') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required autofocus>
                        </div>
                        <div>
                            <label for="nopol" class="block font-medium text-sm text-gray-700">Nomor Polisi</label>
                            <input type="text" name="nopol" id="nopol" value="{{ old('nopol') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required autofocus>
                        </div>
                        <div>
                            <label for="KM" class="block font-medium text-sm text-gray-700">KM</label>
                            <input type="number" name="KM" id="KM" step="0.1" value="{{ old('KM') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required autofocus>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="jenis_mobil" :value="__('Jenis Mobil')" />

                            <select id="jenis_mobil" name="jenis_mobil" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>

                                {{-- Opsi default sebagai placeholder --}}
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

                            {{-- Menampilkan error validasi jika tidak dipilih --}}
                            <x-input-error :messages="$errors->get('jenis_kendaraan')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('kendaraan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Simpan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>