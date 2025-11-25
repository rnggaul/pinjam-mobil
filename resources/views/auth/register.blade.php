<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Divisi -->
        <div class="mt-4">
            {{--
      Label 'for' saya ubah menjadi 'id_divisi' 
      agar cocok dengan id <select> untuk aksesibilitas 
    --}}
            <x-input-label for="id_divisi" :value="__('Divisi')" />

            <select id="id_divisi" name="id_divisi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>

                {{-- Opsi default --}}
                <option value="">Pilih Divisi</option>

                {{-- Loop data dari database --}}
                @foreach ($divisions as $divisi)
                <option value="{{ $divisi->id_divisi }}">{{ $divisi->nama_divisi }}</option>
                @endforeach

            </select>

            {{-- Ini untuk menampilkan error validasi jika 'id_divisi' tidak diisi --}}
            <x-input-error :messages="$errors->get('id_divisi')" class="mt-2" />
        </div>


        <!-- Password -->
        <!-- <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div> -->

        <!-- Confirm Password -->
        <!-- <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div> -->
        <!-- <div class="mt-4">
            {!! htmlFormSnippet() !!}
            <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
        </div> -->
        <div class="flex items-center justify-end mt-4">
            <a href="{{route('dashboard')}}" class="me-3">
                <x-secondary-button>
                    {{ __('Cancel') }}
                </x-secondary-button>
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>