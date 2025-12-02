<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Divisi') }}
        </h2>
    </x-slot>

    {{-- State Alpine.js: Menyimpan status modal, mode edit, data form, dan URL target --}}
    <div class="py-12"
        x-data="{ 
        showModal: false, 
        isEdit: false, 
        modalTitle: 'Tambah Divisi',
        submitUrl: '',
        form: { nama_divisi: '' },
        
        // Fungsi Buka Mode Tambah
        openCreate() {
            this.showModal = true;
            this.isEdit = false;
            this.modalTitle = 'Tambah Divisi Baru';
            this.submitUrl = '{{ route('divisi.store') }}';
            this.form.nama_divisi = '';
            this.clearErrors();
        },

        // Fungsi Buka Mode Edit
        openEdit(divisi, url) {
            this.showModal = true;
            this.isEdit = true;
            this.modalTitle = 'Edit Data Divisi';
            this.submitUrl = url;
            this.form.nama_divisi = divisi.nama_divisi;
            this.clearErrors();
        },

        // Fungsi Reset Error (Helper)
        clearErrors() {
            document.getElementById('error-nama').innerText = '';
            document.getElementById('error-nama').classList.add('hidden');
        },

        // LOGIKA SUBMIT FORM (Dipindahkan ke sini agar aman)
        submitForm(event) {
            let formData = new FormData(event.target);
            let errorText = document.getElementById('error-nama');

            this.clearErrors();

            fetch(this.submitUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    if(data.errors && data.errors.nama_divisi) {
                        errorText.innerText = data.errors.nama_divisi[0];
                        errorText.classList.remove('hidden');
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
     }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-4">
                        {{-- TOMBOL TAMBAH --}}
                        <button @click="openCreate()"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Tambah Divisi Baru
                        </button>
                    </div>

                    {{-- Alert Session PHP (Opsional/Fallback) --}}
                    @if (session('success'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
                    @endif

                    {{-- TABEL DATA --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Divisi</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($divisis as $divisi)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $divisi->nama_divisi }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                    {{-- TOMBOL EDIT --}}
                                    {{-- Kita passing object data divisi dan URL update ke fungsi openEdit --}}
                                    <button
                                        @click="openEdit({{ $divisi }}, '{{ route('divisi.update', $divisi) }}')"
                                        class="text-indigo-600 hover:text-indigo-900 mr-4">
                                        Edit
                                    </button>

                                    {{-- Tombol Hapus (Tetap Form Standar) --}}
                                    <form action="{{ route('divisi.destroy', $divisi) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-6">
                        {{ $divisis->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MODAL FORM (Create & Edit) ================= --}}
        <div x-show="showModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">

            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    {{-- Form menggunakan event handler khusus JS --}}
                    {{-- Perhatikan penambahan @submit.prevent --}}
                    <form id="modalForm" @submit.prevent="submitForm($event)">
                        @csrf
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">

                        {{-- Sisa isi form tetap sama --}}
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="modalTitle"></h3>

                            <div class="mt-4">
                                <label for="nama_divisi" class="block text-sm font-medium text-gray-700">Nama Divisi</label>
                                <input type="text" name="nama_divisi" id="nama_divisi" x-model="form.nama_divisi"
                                    class="mt-1 focus:ring-gray-800 focus:border-gray-800 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p id="error-nama" class="text-red-500 text-xs mt-1 hidden"></p>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-800 text-base font-medium text-white hover:bg-gray-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                            <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT AJAX HANDLER --}}
    <!-- <script>
        document.getElementById('modalForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Ambil data Alpine scope untuk mendapatkan URL yang dinamis
            // 'this' di sini adalah element form HTML
            // Kita gunakan trik Alpine untuk mengambil data scope dari elemen terdekat
            let alpineData = Alpine.$data(document.querySelector('[x-data]'));
            let targetUrl = alpineData.submitUrl;

            let formData = new FormData(this);
            let errorText = document.getElementById('error-nama');
            errorText.classList.add('hidden');
            errorText.innerText = '';

            fetch(targetUrl, {
                    method: 'POST', // Selalu POST, Laravel membaca _method: PUT dari formData
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        if (data.errors && data.errors.nama_divisi) {
                            errorText.innerText = data.errors.nama_divisi[0];
                            errorText.classList.remove('hidden');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script> -->
</x-app-layout>