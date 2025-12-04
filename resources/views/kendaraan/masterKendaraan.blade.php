<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Kendaraan') }}
        </h2>
    </x-slot>

    <div class="py-12"
       <div class="py-12"
    x-data="{ 
        showModal: false, 
        isEdit: false, 
        modalTitle: '',
        submitUrl: '',
        flashMessage: '', // VARIABEL BARU UNTUK ALERT
        
        // DEFINISI FORM
        form: { 
            nama_kendaraan: '', 
            nopol: '', 
            jenis_mobil: '' 
        },

        // FUNGSI INIT (Dijalankan otomatis saat halaman dimuat)
        init() {
            // Cek apakah ada pesan sukses di sessionStorage (dari reload sebelumnya)
            if(sessionStorage.getItem('pesanSukses')) {
                this.flashMessage = sessionStorage.getItem('pesanSukses');
                sessionStorage.removeItem('pesanSukses'); // Hapus agar tidak muncul terus-menerus
                
                // Hilangkan pesan otomatis setelah 5 detik
                setTimeout(() => {
                    this.flashMessage = '';
                }, 5000);
            }
        },
        
        // 1. Fungsi Buka Modal Tambah
        openCreate() {
            this.showModal = true;
            this.isEdit = false;
            this.modalTitle = 'Tambah Kendaraan Baru';
            this.submitUrl = '{{ route('kendaraan.store') }}';
            
            // Reset form
            this.form.nama_kendaraan = '';
            this.form.nopol = '';
            this.form.jenis_mobil = '';
            
            this.clearErrors();
        },

        // 2. Fungsi Buka Modal Edit
        openEdit(item, url) {
            this.showModal = true;
            this.isEdit = true;
            this.modalTitle = 'Edit Data Kendaraan';
            this.submitUrl = url;
            
            // Isi form dengan data lama
            this.form.nama_kendaraan = item.nama_kendaraan;
            this.form.nopol = item.nopol;
            this.form.jenis_mobil = item.jenis_mobil;
            
            this.clearErrors();
        },

        // 3. Helper Hapus Pesan Error
        clearErrors() {
            ['nama_kendaraan', 'nopol', 'jenis_mobil'].forEach(field => {
                let el = document.getElementById('error-' + field);
                if(el) { el.innerText = ''; el.classList.add('hidden'); }
            });
        },

        // 4. LOGIKA SUBMIT
        submitForm(event) {
            let formData = new FormData(event.target);
            
            if(!this.submitUrl) return;

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
                    // --- LOGIKA SIMPAN PESAN & RELOAD ---
                    sessionStorage.setItem('pesanSukses', data.message);
                    window.location.reload();
                    // ------------------------------------
                } else {
                    if(data.errors) {
                        // Loop error validasi Laravel
                        Object.keys(data.errors).forEach(key => {
                            let errorEl = document.getElementById('error-' + key);
                            if(errorEl) {
                                errorEl.innerText = data.errors[key][0];
                                errorEl.classList.remove('hidden');
                            }
                        });
                    } else {
                        alert('Terjadi kesalahan: ' + (data.message || 'Unknown Error'));
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem.');
            });
        }
    }">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                {{-- TOMBOL TAMBAH --}}
                <div class="flex justify-between items-center mb-4">
                    <button @click="openCreate()"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Tambah Kendaraan Baru
                    </button>
                </div>

                {{-- ALERT / FLASH MESSAGE (MUNCUL DI ATAS TABEL) --}}
                <div x-show="flashMessage" 
                     x-transition.duration.500ms
                     style="display: none;"
                     class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline" x-text="flashMessage"></span>
                    <span @click="flashMessage = ''" class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
                </div>

                {{-- TABEL DATA --}}
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kendaraan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Polisi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Mobil</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($kendaraans as $kendaraan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $kendaraan->nama_kendaraan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $kendaraan->nopol }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $kendaraan->jenis_mobil }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                {{-- TOMBOL EDIT --}}
                                <button
                                    @click="openEdit({{ $kendaraan }}, '{{ route('kendaraan.update', $kendaraan) }}')"
                                    class="text-indigo-600 hover:text-indigo-900 mr-4">
                                    Edit
                                </button>

                                <form action="{{ route('kendaraan.destroy', $kendaraan) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data kendaraan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-6">
                    {{ $kendaraans->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FORM --}}
    <div x-show="showModal" style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm
            showModal: false, 
            isEdit: false, 
            modalTitle: '',
            submitUrl: '',
            // DEFINISI FORM (3 Field)
            form: { 
                nama_kendaraan: '', 
                nopol: '', 
                jenis_mobil: '' 
            },
            
            init() {
                if(sessionStorage.getItem('pesanSukses')) {
                this.flashMessage = sessionStorage.getItem('pesanSukses');
                sessionStorage.removeItem('pesanSukses');             
                setTimeout(() => this.flashMessage = '', 5000);
                    }
                },

            // 1. Fungsi Buka Modal Tambah
            openCreate() {
                this.showModal = true;
                this.isEdit = false;
                this.modalTitle = 'Tambah Kendaraan Baru';
                this.submitUrl = '{{ route('kendaraan.store') }}';
                
                // Reset form
                this.form.nama_kendaraan = '';
                this.form.nopol = '';
                this.form.jenis_mobil = '';
                
                this.clearErrors();
            },

            // 2. Fungsi Buka Modal Edit
            openEdit(item, url) {
                this.showModal = true;
                this.isEdit = true;
                this.modalTitle = 'Edit Data Kendaraan';
                this.submitUrl = url;
                
                // Isi form dengan data lama
                this.form.nama_kendaraan = item.nama_kendaraan;
                this.form.nopol = item.nopol;
                this.form.jenis_mobil = item.jenis_mobil;
                
                this.clearErrors();
            },

            // 3. Helper Hapus Pesan Error
            clearErrors() {
                // Sembunyikan semua text error
                ['nama_kendaraan', 'nopol', 'jenis_mobil'].forEach(field => {
                    let el = document.getElementById('error-' + field);
                    if(el) { el.innerText = ''; el.classList.add('hidden'); }
                });
            },

            // 4. LOGIKA SUBMIT
            submitForm(event) {
                let formData = new FormData(event.target);
                
                if(!this.submitUrl) return;

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
                        if(data.errors) {
                            // Loop error untuk menampilkan di field masing-masing
                            Object.keys(data.errors).forEach(key => {
                                let errorEl = document.getElementById('error-' + key);
                                if(errorEl) {
                                    errorEl.innerText = data.errors[key][0];
                                    errorEl.classList.remove('hidden');
                                }
                            });
                        } else {
                            alert('Terjadi kesalahan: ' + (data.message || 'Unknown Error'));
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem.');
                });
            }
         }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- TOMBOL TAMBAH --}}
                    <div class="flex justify-between items-center mb-4">
                        <button @click="openCreate()"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Tambah Kendaraan Baru
                        </button>
                    </div>

                    <div x-show="flashMessage"
                        x-transition
                        class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Berhasil!</strong>
                        <span class="block sm:inline" x-text="flashMessage"></span>

                        <span @click="flashMessage = ''" class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer">
                            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Close</title>
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                            </svg>
                        </span>
                    </div>

                    {{-- TABEL DATA --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kendaraan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Polisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Mobil</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($kendaraans as $kendaraan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $kendaraan->nama_kendaraan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $kendaraan->nopol }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $kendaraan->jenis_mobil }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    {{-- TOMBOL EDIT --}}
                                    <button
                                        @click="openEdit({{ $kendaraan }}, '{{ route('kendaraan.update', $kendaraan) }}')"
                                        class="text-indigo-600 hover:text-indigo-900 mr-4">
                                        Edit
                                    </button>

                                    <form action="{{ route('kendaraan.destroy', $kendaraan) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data kendaraan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-6">
                        {{ $kendaraans->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL FORM --}}
        <div x-show="showModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">

            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <form @submit.prevent="submitForm($event)">
                        @csrf
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="modalTitle"></h3>

                            {{-- Input Nama Kendaraan --}}
                            <div class="mt-4">
                                <label for="nama_kendaraan" class="block text-sm font-medium text-gray-700">Nama Kendaraan</label>
                                <input type="text" name="nama_kendaraan" id="nama_kendaraan" x-model="form.nama_kendaraan"
                                    class="mt-1 focus:ring-gray-800 focus:border-gray-800 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p id="error-nama_kendaraan" class="text-red-500 text-xs mt-1 hidden"></p>
                            </div>

                            {{-- Input Nopol (HANYA MUNCUL JIKA BUKAN EDIT) --}}
                            <template x-if="!isEdit">
                                <div class="mt-4">
                                    <label for="nopol" class="block text-sm font-medium text-gray-700">Nomor Polisi (Plat)</label>
                                    <input type="text" name="nopol" id="nopol" x-model="form.nopol"
                                        class="mt-1 focus:ring-gray-800 focus:border-gray-800 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <p id="error-nopol" class="text-red-500 text-xs mt-1 hidden"></p>
                                </div>
                            </template>

                            {{-- Input Jenis Mobil (Dropdown) --}}
                            <div class="mt-4">
                                <label for="jenis_mobil" class="block text-sm font-medium text-gray-700">Jenis Mobil</label>
                                <select name="jenis_mobil" id="jenis_mobil" x-model="form.jenis_mobil"
                                    class="mt-1 focus:ring-gray-800 focus:border-gray-800 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">

                                    {{-- Opsi Default (Placeholder) --}}
                                    <option value="" disabled>-- Pilih Jenis Mobil --</option>

                                    {{-- Opsi Sesuai ENUM Database --}}
                                    <option value="Sedan">Sedan</option>
                                    <option value="LCGC">LCGC</option>
                                    <option value="SUV">SUV</option>
                                    <option value="MPV">MPV</option>
                                </select>

                                {{-- Tempat Pesan Error --}}
                                <p id="error-jenis_mobil" class="text-red-500 text-xs mt-1 hidden"></p>
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
</x-app-layout>