<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-secondary leading-tight">
            {{ __('Daftar Booking') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 p-4 rounded-lg shadow-sm 
                            transform hover:-translate-y-1 transition-all duration-300" role="alert">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-green-700">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Status Booking Section dengan efek shadow dan hover -->
            <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden" 
                 x-data="{ activeTab: '{{ request()->query('tab', 'active') }}' }">
                <div class="p-6">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-secondary to-secondary/80 
                               bg-clip-text text-transparent mb-6">
                        Status Booking
                    </h2>

                    <!-- Tabs dengan animasi -->
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8">
                            <button @click="activeTab = 'active'; switchTab('active')"
                                    :class="{ 'border-b-2 border-primary text-primary': activeTab === 'active',
                                            'text-gray-500 hover:text-primary': activeTab !== 'active' }"
                                    class="py-4 px-1 text-sm font-medium transition-all duration-300 relative group">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 transition-colors duration-300" 
                                         :class="{ 'text-primary': activeTab === 'active' }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Booking Aktif
                                </span>
                                <!-- Hover effect -->
                                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-primary transform scale-x-0 
                                            group-hover:scale-x-100 transition-transform duration-300"
                                     :class="{ 'scale-x-100': activeTab === 'active' }">
                                </div>
                            </button>

                            <button @click="activeTab = 'history'; switchTab('history')"
                                    :class="{ 'border-b-2 border-primary text-primary': activeTab === 'history',
                                            'text-gray-500 hover:text-primary': activeTab !== 'history' }"
                                    class="py-4 px-1 text-sm font-medium transition-all duration-300 relative group">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 transition-colors duration-300" 
                                         :class="{ 'text-primary': activeTab === 'history' }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Riwayat Booking
                                </span>
                                <!-- Hover effect -->
                                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-primary transform scale-x-0 
                                            group-hover:scale-x-100 transition-transform duration-300"
                                     :class="{ 'scale-x-100': activeTab === 'history' }">
                                </div>
                            </button>
                        </nav>
                    </div>

                    <!-- Active Bookings dengan efek card -->
                    <div x-show="activeTab === 'active'" x-cloak 
                         class="mt-6 space-y-4"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-x-2"
                         x-transition:enter-end="opacity-100 transform translate-x-0">
                        @forelse($activeBookings as $booking)
                            <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.07)] 
                                      hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100
                                      p-6 transform hover:-translate-y-1 transition-all duration-300">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-lg font-semibold text-secondary">{{ $booking->court->name }}</h3>
                                            <span class="px-3 py-1 rounded-full text-sm
                                                @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status == 'waiting_payment') bg-blue-100 text-blue-800
                                                @elseif($booking->status == 'paid') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>
                                        <div class="space-y-2 text-sm text-gray-600">
                                            @if($booking->schedule)
                                                <p>Tanggal: {{ $booking->schedule->schedule_date->format('d M Y') }}</p>
                                                <p>Waktu: {{ $booking->schedule->start_time->format('H:i') }} -
                                                       {{ $booking->schedule->end_time->format('H:i') }} WIB</p>
                                                <p>Durasi: {{ $booking->duration }} Jam</p>
                                            @else
                                                <p class="text-yellow-600">Jadwal belum dipilih</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col md:items-end gap-3">
                                        <p class="text-lg font-semibold text-primary">
                                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                        </p>
                                        <a href="{{ route('user.bookings.show', $booking) }}"
                                           class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                                            <span class="text-sm">Lihat Detail</span>
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 transform hover:-translate-y-1 transition-all duration-300">
                                <div class="bg-primary/5 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4
                                            animate-pulse">
                                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-secondary">Tidak ada booking aktif</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai booking lapangan sekarang</p>
                                <div class="mt-6">
                                    <a href="{{ route('user.courts.index') }}"
                                       class="inline-flex items-center px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Booking Lapangan
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- History Section dengan efek serupa -->
                    <div x-show="activeTab === 'history'" x-cloak 
                         class="mt-6 space-y-4"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-x-2"
                         x-transition:enter-end="opacity-100 transform translate-x-0">
                        @forelse($historyBookings as $bookings)
                            <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.07)] 
                                      hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100
                                      p-6 transform hover:-translate-y-1 transition-all duration-300">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-lg font-semibold text-secondary">{{ $bookings->court->name }}</h3>
                                            <span class="px-3 py-1 rounded-full text-sm
                                                @if($bookings->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($bookings->status == 'waiting_payment') bg-blue-100 text-blue-800 
                                                @elseif($bookings->status == 'paid') bg-green-100 text-green-800
                                                @elseif($bookings->status == 'completed') bg-gray-100 text-gray-800
                                                @elseif($bookings->status == 'cancelled') bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($bookings->status) }}
                                            </span>
                                        </div>
                                        <div class="space-y-2 text-sm text-gray-600">
                                            <p class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $bookings->court->location }}
                                            </p>
                                            @if($bookings->schedule)
                                                <div class="flex items-start space-x-8">
                                                    <p class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        {{ $bookings->schedule->schedule_date->format('d M Y') }}
                                                    </p>
                                                    <p class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $bookings->schedule->start_time->format('H:i') }} -
                                                        {{ $bookings->schedule->end_time->format('H:i') }} WIB
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col md:items-end gap-3">
                                        <p class="text-lg font-semibold text-primary">
                                            Rp {{ number_format($bookings->total_price, 0, ',', '.') }}
                                        </p>
                                        <a href="{{ route('user.bookings.show', $bookings) }}"
                                           class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                                            <span class="text-sm">Lihat Detail</span>
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 transform hover:-translate-y-1 transition-all duration-300">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-secondary">Tidak ada riwayat booking</h3>
                                <p class="mt-1 text-sm text-gray-500">Riwayat booking Anda akan muncul di sini</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bookingTabs', () => ({
        activeTab: 'active',
        
        init() {
            // Cek URL untuk tab yang aktif
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab) {
                this.activeTab = tab;
            }
        },

        switchTab(tab) {
            this.activeTab = tab;
            // Update URL tanpa reload
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);
        }
    }));
});
</script>
@endpush
