<header class="fixed top-0 z-50 w-full bg-white dark:bg-dark-bg-secondary border-b border-gray-200 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start">
                <!-- Tombol Toggle Sidebar -->
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-600 dark:text-gray-400 rounded cursor-pointer lg:hidden hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex ml-2 md:mr-24">
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-primary">Admin Bendella</span>
                </a>
            </div>
            <!-- Menu Profil -->
            <div class="flex items-center">
                <div class="relative ml-3" x-data="{ open: false }">
                    <button @click="open = !open" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                        <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=111827&color=fff" alt="foto pengguna">
                    </button>
                    <!-- Menu dropdown -->
                    <div x-show="open" @click.away="open = false" class="absolute right-0 z-50 mt-2 w-48 text-base list-none bg-white dark:bg-dark-bg-secondary rounded divide-y divide-gray-100 dark:divide-gray-700 shadow">
                        <div class="py-3 px-4">
                            <span class="block text-sm text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
                            <span class="block text-sm font-medium text-gray-500 truncate dark:text-gray-400">{{ auth()->user()->email }}</span>
                        </div>
                        <ul class="py-1">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
