<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-secondary leading-tight">
            {{ __('Keranjang Booking') }}
        </h2>
    </x-slot>

    <div x-data="bookingList" class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm transform hover:-translate-y-1 transition-all duration-300" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                <div class="p-6">
                    @if($bookings->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-secondary">Keranjang Kosong</h3>
                            <p class="mt-2 text-sm text-gray-500">Anda belum memiliki booking di keranjang</p>
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
                    @else
                        <div class="space-y-6">
                            @foreach($bookings as $booking)
                                <div class="bg-white rounded-xl shadow-[0_10px_25px_-3px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_35px_-5px_rgba(0,0,0,0.12)] transform hover:-translate-y-1 transition-all duration-300">
                                    <div class="flex flex-col md:flex-row gap-6 p-6">
                                        <div class="w-full md:w-48">
                                            <div class="relative h-32 rounded-xl overflow-hidden">
                                                @php
                                                    $photos = explode(',', $booking->court->photo);
                                                @endphp
                                                <div class="swiper courtImageSlider">
                                                    <div class="swiper-wrapper">
                                                        @foreach($photos as $photo)
                                                            <div class="swiper-slide">
                                                                <img src="{{ asset(trim($photo)) }}"
                                                                     alt="{{ $booking->court->name }}"
                                                                     class="w-full h-32 object-cover rounded-lg">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="swiper-pagination"></div>
                                                    <div class="swiper-button-next"></div>
                                                    <div class="swiper-button-prev"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-1 flex flex-col justify-between">
                                            <div>
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <h3 class="text-xl font-bold text-gray-800 mb-2">
                                                            {{ $booking->court->name }}
                                                        </h3>
                                                        <p class="text-gray-600 flex items-center gap-2">
                                                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                            {{ $booking->court->location }}
                                                        </p>
                                                    </div>
                                                    <button @click="deleteBooking({{ $booking->id }})" 
                                                            type="button"
                                                            class="text-red-600 hover:text-red-800 p-2 hover:bg-red-50 rounded-lg transition-all duration-300">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="mt-4">
                                                    @if($booking->schedule)
                                                        <div class="space-y-1 px-4 py-2 bg-green-50 text-green-700 rounded-lg shadow-sm">
                                                            <p class="flex items-center gap-2">
                                                                <span class="font-medium">Tanggal:</span>
                                                                {{ $booking->schedule->schedule_date->isoFormat('D MMMM') }}
                                                            </p>
                                                            <p class="flex items-center gap-2">
                                                                <span class="font-medium">Jam:</span>
                                                                {{ $booking->schedule->start_time->format('H:i') }} - 
                                                                {{ $booking->schedule->end_time->format('H:i') }}
                                                            </p>
                                                            <p class="flex items-center gap-2">
                                                                <span class="font-medium">Durasi:</span>
                                                                {{ $booking->duration }} jam
                                                            </p>
                                                        </div>
                                                    @else
                                                        <button type="button"
                                                                @click="$dispatch('open-schedule-modal', { bookingId: {{ $booking->id }} })"
                                                                class="inline-flex items-center px-4 py-2 text-primary border-2 border-primary rounded-lg hover:bg-primary hover:text-white transition-all duration-300 shadow-sm hover:shadow-md">
                                                            <span class="flex items-center">
                                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                </svg>
                                                                Pilih Jadwal
                                                            </span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <p class="text-2xl font-bold text-primary">
                                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($bookings->isNotEmpty())
                                <div class="mt-8 border-t pt-8">
                                    <div class="bg-white rounded-xl shadow-[0_10px_25px_-3px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_35px_-5px_rgba(0,0,0,0.12)] transform hover:-translate-y-1 transition-all duration-300 p-6">
                                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                            <div>
                                                <p class="text-sm text-gray-600">Total Pembayaran</p>
                                                <p class="text-2xl font-bold text-primary">
                                                    Rp {{ number_format($bookings->sum('total_price'), 0, ',', '.') }}
                                                </p>
                                                @unless($canCheckout)
                                                    <p class="text-sm text-yellow-600 mt-1">
                                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                        Pilih jadwal untuk semua lapangan terlebih dahulu
                                                    </p>
                                                @endunless
                                            </div>
                                            <form action="{{ route('user.bookings.checkout') }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        @unless($canCheckout) disabled @endunless
                                                        class="w-full md:w-auto px-8 py-3 bg-primary text-white rounded-lg transition
                                                               @unless($canCheckout)
                                                                   opacity-50 cursor-not-allowed
                                                               @else
                                                                   hover:bg-primary/90
                                                               @endunless">
                                                    Lanjut ke Pembayaran
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!$bookings->isEmpty())
        @include('user.bookings.schedule-modal', ['bookings' => $bookings])
    @endif

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
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bookingList', () => ({
            async deleteBooking(bookingId) {
                if (!confirm('Apakah Anda yakin ingin menghapus booking ini?')) {
                    return;
                }

                try {
                    const response = await fetch(`/user/bookings/${bookingId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // Reload halaman setelah berhasil hapus
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Gagal menghapus booking');
                    }
                } catch (error) {
                    console.error('Error deleting booking:', error);
                    alert(error.message || 'Terjadi kesalahan saat menghapus booking');
                }
            }
        }));
    });
    </script>
    @endpush
</x-app-layout>
