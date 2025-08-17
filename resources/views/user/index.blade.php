<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-secondary leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div x-data="photoModal()" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @foreach($courts as $court)
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <div class="flex flex-col lg:flex-row">
                    <!-- Gambar Utama dan Thumbnail -->
                    <div class="lg:w-2/3">
                        <div class="relative">
                            <img src="{{ explode(',', $court->photo)[0] }}"
                                alt="Main view of {{ $court->name }}"
                                class="rounded-lg w-full h-[400px] object-cover" />

                            <!-- Status Badge -->
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $court->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $court->status === 'active' ? 'Tersedia' : 'Tidak Tersedia' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/3 lg:pl-4 mt-4 lg:mt-0">
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(array_slice(explode(',', $court->photo), 1, 3) as $photo)
                            <img src="{{ $photo }}"
                                alt="View of {{ $court->name }}"
                                class="rounded-lg h-[120px] object-cover cursor-pointer"
                                @click="openModal('{{ $court->id }}', {{ json_encode(explode(',', $court->photo)) }})" />
                            @endforeach
                        </div>
                        <button type="button"
                            @click.prevent="openModal('{{ $court->id }}', {{ json_encode(explode(',', $court->photo)) }})"
                            class="mt-2 bg-gray-800 text-white py-2 px-4 rounded-lg w-full hover:bg-gray-700 transition">
                            Lihat semua foto
                        </button>
                    </div>
                </div>

                <!-- Informasi Lapangan -->
                <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ $court->name }}
                        </h1>
                        <p class="text-gray-600 mt-1">
                            {{ $court->location }}
                        </p>
                        <div class="flex items-center mt-2">
                            <span class="bg-gray-200 text-gray-800 text-sm font-semibold mr-2 px-2.5 py-0.5 rounded">
                                Basketball
                            </span>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mt-4">
                            <h2 class="text-xl font-semibold text-gray-800">
                                Deskripsi
                            </h2>
                            <p class="text-gray-700 mt-2">
                                {{ $court->description }}
                            </p>
                        </div>

                        <!-- Fasilitas -->
                        <div class="mt-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-3">
                                Fasilitas
                            </h2>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(['Cafe & Resto', 'Jual Makanan Ringan', 'Jual Makanan Berat', 'Musholla', 'Parkir Motor & Mobil', 'Toilet',  'Wifi', 'Shower', 'Jual Minuman', 'Ruang Ganti', 'Tribun Penonton', 'Score Board with Shot Clock'] as $facility)
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-600">{{ $facility }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Harga dan Booking -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-6 border">
                            <p class="text-gray-600">
                                Mulai dari
                            </p>
                            <p class="text-2xl font-bold text-primary">
                                Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}
                                <span class="text-sm font-normal text-gray-600">
                                    /jam
                                </span>
                            </p>
                            <p class="text-sm text-gray-600">Harga Member: Rp {{ number_format($court->member_price_per_hour, 0, ',', '.') }} / jam</p>
                            @if($court->status === 'active')
                            <form action="{{ route('user.bookings.store', ['court' => $court->id]) }}" method="post">
                                @csrf
                                <button type="submit"
                                    class="mt-4 bg-red-700 text-white py-3 px-4 rounded-lg w-full hover:bg-red-800 transition">
                                    Cek Ketersediaan
                                </button>
                            </form>
                            @endif

                            <!-- Keuntungan Booking -->
                            <div class="mt-6">
                                <h3 class="font-semibold text-gray-800 mb-3">
                                    Booking lewat aplikasi lebih banyak keuntungan!
                                </h3>
                                <ul class="space-y-2">
                                    <li class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Opsi pembayaran down payment (DP)*
                                    </li>
                                    <li class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Reschedule jadwal booking**
                                    </li>
                                    <li class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Lebih banyak promo & voucher
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Single Modal untuk semua foto -->
        <div x-show="isOpen"
            x-cloak
            @keydown.escape.window="closeModal()"
            class="fixed inset-0 z-50 overflow-hidden bg-black/95">

            <!-- Modal Content -->
            <div class="relative h-full flex flex-col items-center justify-center">
                <!-- Close Button -->
                <button @click.prevent="closeModal()"
                    class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Main Image Container -->
                <div class="relative w-full h-[calc(100vh-200px)] flex items-center justify-center px-20">
                    <!-- Previous Button -->
                    <button @click.prevent="prevSlide()"
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-10 bg-black/50 rounded-full p-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <!-- Next Button -->
                    <button @click.prevent="nextSlide()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-10 bg-black/50 rounded-full p-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Main Image -->
                    <template x-for="(photo, index) in photos" :key="index">
                        <img :src="photo"
                            :alt="'Photo ' + (index + 1)"
                            class="max-h-full max-w-full object-contain transition-opacity duration-300"
                            :class="{ 'opacity-0': currentSlide !== index }"
                            x-show="currentSlide === index">
                    </template>
                </div>

                <!-- Thumbnail Navigation -->
                <div class="w-full bg-black/50 py-4 mt-4">
                    <div class="max-w-4xl mx-auto px-4">
                        <div class="flex justify-center items-center gap-2">
                            <template x-for="(photo, index) in photos" :key="index">
                                <button @click.prevent="currentSlide = index"
                                    class="relative w-20 h-20 overflow-hidden rounded transition-all"
                                    :class="currentSlide === index ? 'ring-2 ring-white' : 'opacity-50 hover:opacity-100'">
                                    <img :src="photo"
                                        :alt="'Thumbnail ' + (index + 1)"
                                        class="w-full h-full object-cover">
                                    <div x-show="currentSlide === index"
                                        class="absolute inset-0 border-2 border-white"></div>
                                </button>
                            </template>
                        </div>
                        <div class="text-center text-white mt-2 text-sm">
                            <span x-text="currentSlide + 1"></span> / <span x-text="photos.length"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('photoModal', () => ({
                isOpen: false,
                currentSlide: 0,
                photos: [],
                currentCourtId: null,

                openModal(courtId, photos) {
                    this.currentCourtId = courtId;
                    this.photos = photos;
                    this.currentSlide = 0;
                    this.isOpen = true;
                },

                closeModal() {
                    this.isOpen = false;
                },

                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.photos.length;
                },

                prevSlide() {
                    this.currentSlide = (this.currentSlide - 1 + this.photos.length) % this.photos.length;
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>