<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Driver') }}
        </h2>
    </x-slot>

    <div class="py-12" 
         x-data="{ 
            showModal: false, 
            isEdit: false, 
            modalTitle: '',
            submitUrl: '',
            // DEFINISI FORM (Sesuaikan jika ada field lain seperti no_hp)
            form: { 
                nama_driver: '' 
            },
            
            // 1. Fungsi Buka Modal Tambah
            openCreate() {
                this.showModal = true;
                this.isEdit = false;
                this.modalTitle = 'Tambah Driver Baru';
                this.submitUrl = '{{ route('driver.store') }}';
                this.form.nama_driver = ''; // Reset form
                this.clearErrors();
            },

            // 2. Fungsi Buka Modal Edit
            openEdit(item, url) {
                this.showModal = true;
                this.isEdit = true;
                this.modalTitle = 'Edit Data Driver';
                this.submitUrl = url;
                this.form.nama_driver = item.nama_driver; // Isi data lama
                this.clearErrors();
            },

            // 3. Helper Hapus Pesan Error
            clearErrors() {
                let errorEl = document.getElementById('error-nama_driver');
                if(errorEl) { errorEl.innerText = ''; errorEl.classList.add('hidden'); }
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
                        if(data.errors && data.errors.nama_driver) {
                            let errorEl = document.getElementById('error-nama_driver');
                            errorEl.innerText = data.errors.nama_driver[0];
                            errorEl.classList.remove('hidden');
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
                    <div class="mb-4 flex justify-start">
                        <button @click="openCreate()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Tambah Driver Baru
                        </button>
                    </div>

                    {{-- TABEL DATA --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Driver</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($drivers as $driver)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $driver->nama_driver }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            {{-- TOMBOL EDIT --}}
                                            <button 
                                                @click="openEdit({{ $driver }}, '{{ route('driver.update', $driver) }}')"
                                                class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                            </button>

                                            {{-- TOMBOL HAPUS --}}
                                            <form action="{{ route('driver.destroy', $driver) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus driver ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Belum ada data driver.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-6">
                            {{ $drivers->appends(request()->query())->links() }}
                        </div>
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
                            
                            {{-- Input Nama Driver --}}
                            <div class="mt-4">
                                <label for="nama_driver" class="block text-sm font-medium text-gray-700">Nama Driver</label>
                                <input type="text" name="nama_driver" id="nama_driver" x-model="form.nama_driver"
                                    class="mt-1 focus:ring-gray-800 focus:border-gray-800 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p id="error-nama_driver" class="text-red-500 text-xs mt-1 hidden"></p>
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