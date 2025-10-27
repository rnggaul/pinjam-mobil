{{-- resources/views/admin/divisi/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Divisi') }}
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

                    {{-- 
                      1. Ganti ACTION ke 'divisi.update' dan kirim $divisi
                      2. Ganti METHOD ke 'POST' (secara teknis)
                    --}}
                    <form action="{{ route('divisi.update', $divisi) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- 3. Tambahkan method PUT untuk update --}}

                        <div>
                            <x-input-label for="nama_divisi" :value="__('Nama Divisi')" />
                            
                            {{-- 4. Isi VALUE dengan data lama --}}
                            <input type="text" 
                                   name="nama_divisi" 
                                   id="nama_divisi" 
                                   value="{{ old('nama_divisi', $divisi->nama_divisi) }}" 
                                   class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                   required 
                                   autofocus>
                                   
                            <x-input-error :messages="$errors->get('nama_divisi')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('divisi.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
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