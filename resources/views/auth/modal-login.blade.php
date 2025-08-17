<div x-show="$store.modal.login"
     x-cloak
     x-transition
     class="fixed inset-0 z-50 overflow-y-auto"
     @keydown.escape.window="$store.modal.login = false">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity"
             aria-hidden="true"
             @click="$store.modal.login = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl"
             @click.away="$store.modal.login = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-secondary">Masuk</h3>
                <button @click="$store.modal.login = false" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @include('auth.login-form')
        </div>
    </div>
</div>
