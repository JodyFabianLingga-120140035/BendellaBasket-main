<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white dark:bg-dark-bg-secondary border-r border-gray-200 dark:border-gray-700 lg:translate-x-0" 
       :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
       aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-dark-bg-secondary">
        <ul class="space-y-2 font-medium">
            <li>
                <a href="{{ route('home') }}" class="flex items-center p-2 text-gray-900 dark:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('home') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                        <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                    </svg>
                    <span class="ml-3">Beranda</span>
                </a>
            </li>
            <li>
                <button type="button" 
                        onclick="toggleDropdown('dropdown-master')"
                        class="flex items-center w-full p-2 text-gray-900 dark:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.court') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.906 9c.382 0 .749.057 1.094.162V9a3 3 0 00-3-3h-3V4a4 4 0 00-4-4h-4a4 4 0 00-4 4v2H0a3 3 0 00-3 3v11a3 3 0 003 3h18a3 3 0 003-3v-.162A2.976 2.976 0 0119.906 15V9zM6 4a2 2 0 012-2h4a2 2 0 012 2v2H6V4zm-3 8h3v1a1 1 0 102 0v-1h6v1a1 1 0 102 0v-1h3a1 1 0 110 2H3a1 1 0 110-2z"/>
                    </svg>
                    <span class="flex-1 ml-3 text-left whitespace-nowrap">Master Data</span>
                    <svg id="dropdown-master-icon" class="w-3 h-3 transition-transform duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                <ul id="dropdown-master" class="{{ request()->routeIs('admin.court') || request()->routeIs('admin.schedule') || request()->routeIs('admin.booking') || request()->routeIs('admin.payment') || request()->routeIs('admin.status') ? '' : 'hidden' }} py-2 space-y-2">
                    <li>
                        <a href="{{ route('admin.court') }}" class="flex items-center w-full p-2 text-gray-900 dark:text-white rounded-lg pl-11 hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.court') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                            </svg>
                            Lapangan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.schedule') }}" class="flex items-center w-full p-2 text-gray-900 dark:text-white rounded-lg pl-11 hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.schedule') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                            </svg>
                            Jadwal
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.booking') }}" class="flex items-center w-full p-2 text-gray-900 dark:text-white rounded-lg pl-11 hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.booking') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Booking Lapangan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.status') }}" class="flex items-center w-full p-2 text-gray-900 dark:text-white rounded-lg pl-11 hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.status') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Status Pembayaran
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.payment') }}" class="flex items-center w-full p-2 text-gray-900 dark:text-white rounded-lg pl-11 hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.payment') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Metode Pembayaran
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.user') }}" class="flex items-center p-2 text-gray-900 dark:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.user') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                        <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                    </svg>
                    <span class="ml-3">Pengguna</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.laporan') }}" class="flex items-center p-2 text-gray-900 dark:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.laporan') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17 9H7V7h10m0 6H7v-2h10m-3 6H7v-2h7M12 3a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h9m10 0h-2v2h2v15H5v-2H3v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/>
                    </svg>
                    <span class="ml-3">Laporan</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
