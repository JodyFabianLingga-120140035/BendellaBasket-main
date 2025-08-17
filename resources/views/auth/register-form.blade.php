<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-4">
        <label for="name" class="block text-secondary font-medium mb-2">Nama Lengkap</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required
               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-primary">
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="mb-4">
        <label for="email" class="block text-secondary font-medium mb-2">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required
               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-primary">
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="mb-4">
        <label for="phone" class="block text-secondary font-medium mb-2">Nomor Telepon</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-primary">
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <div class="mb-4">
        <label for="password" class="block text-secondary font-medium mb-2">Password</label>
        <input type="password" name="password" id="password" required
               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-primary">
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mb-6">
        <label for="password_confirmation" class="block text-secondary font-medium mb-2">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required
               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-primary">
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-primary/90 transition">
        Daftar
    </button>

    <div class="mt-6 text-center text-sm text-secondary">
        Sudah punya akun?
        <button type="button"
                @click="$store.modal.register = false; $nextTick(() => { $store.modal.login = true })"
                class="text-primary hover:text-primary/80 font-medium">
            Masuk
        </button>
    </div>
</form>
