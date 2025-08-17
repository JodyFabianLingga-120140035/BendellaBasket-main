<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-4">
        <label for="email" class="block text-secondary font-medium mb-2">Email</label>
        <input type="email" name="email" id="email" required
               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-primary">
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="mb-4">
        <label for="password" class="block text-secondary font-medium mb-2">Password</label>
        <input type="password" name="password" id="password" required
               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-primary">
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="flex items-center mb-6">
        <label class="flex items-center">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary focus:ring-primary">
            <span class="ml-2 text-sm text-secondary">Ingat Saya</span>
        </label>
    </div>

    <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-primary/90 transition">
        Masuk
    </button>

    <div class="mt-6 text-center text-sm text-secondary">
        Belum punya akun?
        <button type="button"
                @click="$store.modal.login = false; $nextTick(() => { $store.modal.register = true })"
                class="text-primary hover:text-primary/80 font-medium">
            Daftar
        </button>
    </div>
</form>
