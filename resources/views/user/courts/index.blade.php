<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-secondary leading-tight">
            {{ __('Daftar Lapangan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter Section -->
            <div class="mb-8 bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-all duration-300">
                <form action="{{ route('user.courts.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari lapangan atau lokasi..."
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="flex gap-4">
                        <select name="sort"
                                class="px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            <option value="">Urutkan</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        </select>
                        <button type="submit"
                                class="px-8 py-3 bg-primary text-white rounded-xl hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-300">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Courts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($courts as $court)
                    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.12)] transition-all duration-300 overflow-hidden transform hover:-translate-y-1">
                        <div class="relative h-[280px] group">
                            @php
                                $photos = explode(',', $court->photo);
                            @endphp
                            <div class="swiper courtImageSlider h-full">
                                <div class="swiper-wrapper">
                                    @foreach($photos as $photo)
                                        <div class="swiper-slide">
                                            <img src="{{ asset(trim($photo)) }}"
                                                 alt="{{ $court->name }}"
                                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        </div>
                                    @endforeach
                                </div>
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4 z-10">
                                    <span class="px-4 py-2 rounded-full text-sm font-medium backdrop-blur-md
                                        {{ $court->status === 'active' ? 'bg-green-100/90 text-green-800' : 'bg-red-100/90 text-red-800' }}">
                                        {{ $court->status === 'active' ? 'Tersedia' : 'Tidak Tersedia' }}
                                    </span>
                                </div>
                                <div class="swiper-pagination"></div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $court->name }}</h3>
                            <p class="text-gray-600 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $court->location }}
                            </p>
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-500">Mulai dari</p>
                                    <p class="text-xl font-bold text-primary">
                                        Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}
                                        <span class="text-sm font-normal text-gray-500">/jam</span>
                                    </p>
                                </div>
                                <form action="{{ route('user.bookings.store', $court->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="court_id" value="{{ $court->id }}">
                                    <button type="submit"
                                            class="px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary/90 transition-all duration-300 transform hover:scale-105">
                                        Booking
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada lapangan</h3>
                        <p class="mt-1 text-sm text-gray-500">Tidak ada lapangan yang tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($courts->hasPages())
                <div class="mt-8">
                    {{ $courts->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper('.courtImageSlider', {
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });
    </script>
    @endpush
</x-app-layout>
