@extends('admin.index')

@section('main')
<div class="p-4 bg-white dark:bg-dark-bg-secondary rounded-lg shadow-sm mb-4">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Ringkasan Dashboard</h2>
    
    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        <!-- Total Users Card -->
        <div class="p-4 bg-primary/10 dark:bg-primary/10 rounded-lg">
            <div class="flex items-center">
                <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-primary dark:text-primary/90 bg-primary/30 dark:bg-primary/20 rounded-lg">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="mb-2 text-xl font-medium text-gray-900 dark:text-white">{{ $users }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">Total Pengguna</p>
                </div>
            </div>
        </div>

        <!-- Active Bookings Card -->
        <div class="p-4 bg-green-100 dark:bg-green-800 rounded-lg">
            <div class="flex items-center">
                <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-green-600 dark:text-green-200 bg-green-200 dark:bg-green-700 rounded-lg">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="mb-2 text-xl font-medium text-gray-900 dark:text-white">{{ $bookings }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">Pemesanan Aktif</p>
                </div>
            </div>
        </div>

        <!-- Total Court Card -->
        <div class="p-4 bg-blue-100 dark:bg-blue-800 rounded-lg">
            <div class="flex items-center">
                <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-blue-600 dark:text-blue-200 bg-blue-200 dark:bg-blue-700 rounded-lg">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        <path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm0 14.5c-3.59 0-6.5-2.91-6.5-6.5S8.41 5.5 12 5.5s6.5 2.91 6.5 6.5-2.91 6.5-6.5 6.5z"/>
                        <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm0 8c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="mb-2 text-xl font-medium text-gray-900 dark:text-white">{{ $basket_courts }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">Total Lapangan Basket</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="mt-8">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Aktivitas Terbaru</h3>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentActivities as $activity)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <!-- Status Badge -->
                                    @switch($activity['payment_status'])
                                        @case('pending')
                                            <span class="px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-300 rounded-full">
                                                Pending
                                            </span>
                                            @break
                                        @case('confirmed')
                                            <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 dark:bg-green-900 dark:text-green-300 rounded-full">
                                                Dibayar
                                            </span>
                                            @break
                                        @case('failed')
                                            <span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 dark:bg-red-900 dark:text-red-300 rounded-full">
                                                Gagal
                                            </span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 dark:bg-gray-900 dark:text-gray-300 rounded-full">
                                                {{ ucfirst($activity['payment_status']) }}
                                            </span>
                                    @endswitch
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $activity['user_name'] }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Rp {{ number_format($activity['amount'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-500 dark:text-gray-400" title="{{ $activity['created_at'] }}">
                                    {{ $activity['time_ago'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        Belum ada aktivitas
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection