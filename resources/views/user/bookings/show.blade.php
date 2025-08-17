<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl bg-gradient-to-r from-secondary to-secondary/80 bg-clip-text text-transparent">
            {{ __('Detail Booking') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages dengan animasi -->
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

            <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                <div class="p-6">
                    <!-- Header dengan Kode Booking dan Status -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center gap-4">
                            <h3 class="text-xl font-bold bg-gradient-to-r from-secondary to-secondary/80 
                                      bg-clip-text text-transparent">
                                Booking #{{ $booking->booking_code }}
                            </h3>
                            <span class="px-4 py-1.5 rounded-full text-sm font-medium inline-flex items-center gap-2
                                @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status == 'waiting_payment') bg-blue-100 text-blue-800
                                @elseif($booking->status == 'paid') bg-green-100 text-green-800
                                @elseif($booking->status == 'completed') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800 @endif">
                                <span class="w-2 h-2 rounded-full 
                                    @if($booking->status == 'pending') bg-yellow-400
                                    @elseif($booking->status == 'waiting_payment') bg-blue-400
                                    @elseif($booking->status == 'paid') bg-green-400
                                    @elseif($booking->status == 'completed') bg-gray-400
                                    @else bg-red-400 @endif">
                                </span>
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('user.bookings.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl 
                                      hover:bg-gray-200 transition-all duration-300 transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Kembali
                            </a>
                            @if($booking->status == 'waiting_payment')
                                <a href="{{ route('user.bookings.payment') }}"
                                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary to-primary/90 
                                          text-white rounded-xl hover:shadow-lg hover:shadow-primary/30 
                                          transition-all duration-300 transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Bayar
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Informasi Lapangan dengan efek hover -->
                    <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.07)] 
                              hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100
                              p-6 mb-6 transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Image Slider dengan efek fade -->
                            <div class="w-full md:w-1/3">
                                @php
                                    $photos = explode(',', $booking->court->photo);
                                @endphp
                                <div class="swiper courtImageSlider rounded-xl overflow-hidden">
                                    <div class="swiper-wrapper">
                                        @foreach($photos as $photo)
                                            <div class="swiper-slide">
                                                <img src="{{ asset(trim($photo)) }}"
                                                     alt="{{ $booking->court->name }}"
                                                     class="w-full h-48 object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-pagination"></div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                </div>
                            </div>

                            <!-- Court Info -->
                            <div class="flex-1 space-y-4">
                                <h4 class="text-xl font-bold text-gray-800">{{ $booking->court->name }}</h4>
                                <p class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $booking->court->location }}
                                </p>
                                <p class="text-gray-600">{{ $booking->court->description }}</p>
                                <p class="text-xl font-bold bg-gradient-to-r from-primary to-primary/80 
                                          bg-clip-text text-transparent">
                                    Rp {{ number_format($booking->court->price_per_hour, 0, ',', '.') }}/jam
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Section -->
                    @if($booking->schedule)
                        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.07)] 
                                  hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100
                                  p-6 mb-6 transform hover:-translate-y-1 transition-all duration-300">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-800">Jadwal Booking</h3>
                                @if($booking->status == 'paid')
                                    <div x-data>
                                        <button type="button"
                                                @click="$dispatch('open-schedule-modal')"
                                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary 
                                                       to-primary/90 text-white rounded-xl hover:shadow-lg 
                                                       hover:shadow-primary/30 transition-all duration-300 
                                                       transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Ubah Jadwal
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <!-- Schedule Info Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-600">Tanggal</p>
                                        <p class="font-medium">{{ $booking->schedule->schedule_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-600">Waktu</p>
                                        <p class="font-medium">
                                            {{ $booking->schedule->start_time->format('H:i') }} -
                                            {{ $booking->schedule->end_time->format('H:i') }} WIB
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-600">Durasi</p>
                                        <p class="font-medium">{{ $booking->duration }} Jam</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Payment Info Section -->
                    <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.07)] 
                              hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100
                              p-6 transform hover:-translate-y-1 transition-all duration-300">
                        <h3 class="text-lg font-semibold text-secondary mb-4">Informasi Pembayaran</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="bg-primary/5 rounded-lg p-4">
                                    <p class="text-sm text-gray-600">Total Pembayaran</p>
                                    <p class="text-2xl font-bold text-primary">
                                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </p>
                                </div>
                                @if($booking->down_payment)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600">Down Payment</p>
                                    <p class="text-lg font-semibold">
                                        Rp {{ number_format($booking->down_payment, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600">Sisa Pembayaran</p>
                                    <p class="text-lg font-semibold">
                                        Rp {{ number_format($booking->total_price - $booking->down_payment, 0, ',', '.') }}
                                    </p>
                                </div>
                                @endif
                            </div>

                            @if($booking->paymentHistory->isNotEmpty())
                            <div class="space-y-4">
                                <h4 class="font-medium text-secondary">Riwayat Pembayaran</h4>
                                @foreach($booking->paymentHistory as $payment)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-medium">{{ $payment->paymentMethod->name }}</p>
                                            <p class="text-sm text-gray-600">
                                                {{ ucfirst($payment->payment_type) }}
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded
                                                    @if($payment->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($payment->payment_status == 'confirmed') bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($payment->payment_status) }}
                                        </span>
                                    </div>
                                    <p class="text-primary font-medium">
                                        Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $payment->payment_date->format('d M Y H:i') }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Modal -->
    <div x-data="scheduleModal({{ $booking->id }}, {{ $booking->court_id }}, {{ $booking->duration }})"
         x-show="isOpen"
         x-cloak
         @open-schedule-modal.window="openModal($event.detail.bookingId)"
         @keydown.escape.window="closeModal"
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Backdrop dengan blur effect -->
            <div class="fixed inset-0 transition-opacity backdrop-blur-sm" aria-hidden="true"
                 @click="closeModal">
                <div class="absolute inset-0 bg-gray-900/70"></div>
            </div>

            <!-- Modal Content -->
            <div class="relative inline-block w-full max-w-4xl overflow-hidden text-left align-middle transition-all 
                        transform bg-white shadow-[0_10px_40px_-5px_rgba(0,0,0,0.3)] rounded-2xl">
                <!-- Header dengan gradient -->
                <div class="bg-gradient-to-r from-primary/10 to-transparent p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800" x-text="selectedCourt ? selectedCourt.name : ''"></h3>
                            <p class="text-gray-600 mt-1 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-text="selectedCourt ? selectedCourt.location : ''"></span>
                            </p>
                        </div>
                        <button @click="closeModal" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                            <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Calendar Navigation -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex space-x-3 overflow-x-auto pb-2 scrollbar-hide">
                            <template x-for="date in dates" :key="date.value">
                                <button type="button"
                                        @click="selectDate(date.value)"
                                        :class="{'bg-primary shadow-lg shadow-primary/30 transform scale-105 text-white': selectedDate === date.value,
                                                'bg-white hover:bg-gray-50 hover:shadow-md text-gray-800': selectedDate !== date.value}"
                                        class="flex flex-col items-center px-4 py-3 rounded-xl border transition-all duration-300 min-w-[90px]">
                                    <span class="text-xs" x-text="date.day"></span>
                                    <span class="text-lg font-bold mt-1" x-text="date.date"></span>
                                    <span class="text-xs font-medium" x-text="date.month"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Time slots dengan efek mengambang -->
                    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3 mt-4">
                        <template x-for="slot in availableTimeSlots" :key="slot.time">
                            <button type="button"
                                    @click="selectTimeSlot(slot)"
                                    :disabled="slot.status === 'booked' || slot.status === 'holiday'"
                                    :class="getTimeSlotClasses(slot)"
                                    class="p-4 rounded-xl text-sm font-medium transition-all duration-300 relative overflow-hidden
                                           shadow-[0_2px_10px_-3px_rgba(0,0,0,0.07)] hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] 
                                           border border-gray-100 hover:border-gray-200
                                           transform hover:scale-105 hover:-translate-y-1">
                                <div class="text-base font-bold" x-text="formatTime(slot.time)"></div>
                                <div class="text-xs mt-1">1 Jam</div>
                                <div class="text-xs mt-1 font-medium" 
                                     :class="{
                                         'text-red-500': slot.status === 'booked',
                                         'text-yellow-500': slot.status === 'holiday',
                                         'text-green-500': slot.status === 'available'
                                     }"
                                     x-text="getStatusText(slot.status)">
                                </div>
                                <!-- Booked overlay -->
                                <div x-show="slot.status === 'booked'"
                                     class="absolute inset-0 backdrop-blur-[2px] bg-gradient-to-b from-gray-50/80 to-gray-100/80
                                            flex items-center justify-center">
                                    <span class="text-xs font-medium px-3 py-1.5 rounded-full
                                               bg-gradient-to-r from-red-50 to-pink-50 
                                               border border-red-200/50 shadow-sm">
                                        Sudah Dibooking
                                    </span>
                                </div>
                                <!-- Holiday overlay -->
                                <div x-show="slot.status === 'holiday'"
                                     class="absolute inset-0 backdrop-blur-[2px] bg-gradient-to-b from-gray-50/80 to-gray-100/80
                                            flex items-center justify-center">
                                    <span class="text-xs font-medium px-3 py-1.5 rounded-full
                                               bg-gradient-to-r from-yellow-50 to-orange-50 
                                               border border-yellow-200/50 shadow-sm">
                                        Libur
                                    </span>
                                </div>
                            </button>
                        </template>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit"
                                @click="updateSchedule"
                                :disabled="!canSubmit"
                                class="w-full px-4 py-3 bg-gradient-to-r from-primary to-primary/90 text-white 
                                       rounded-xl transition-all duration-300 transform hover:scale-105
                                       hover:shadow-lg hover:shadow-primary/30 disabled:opacity-50 
                                       disabled:cursor-not-allowed disabled:transform-none">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('scheduleModal', (bookingId, courtId, duration) => ({
            isOpen: false,
            bookingId: bookingId,
            courtId: courtId,
            duration: duration,
            selectedCourt: null,
            availableTimeSlots: [],
            selectedSlots: [],
            dates: [],
            selectedDate: null,
            errorMessage: null,

            async openModal(bookingId) {
                console.log('Opening modal for booking:', bookingId);
                this.isOpen = true;
                await this.fetchCourtDetails();
                await this.fetchSchedules();
            },

            async fetchCourtDetails() {
                try {
                    console.log('Fetching court details for ID:', this.courtId);
                    const response = await fetch(`/api/courts/${this.courtId}`);
                    const data = await response.json();
                    
                    console.log('Court details received:', data);
                    
                    if (!response.ok) {
                        throw new Error('Failed to fetch court details');
                    }
                    
                    this.selectedCourt = {
                        name: data.name,
                        location: data.location,
                        price_per_hour: data.price_per_hour
                    };
                } catch (error) {
                    console.error('Error fetching court details:', error);
                    this.errorMessage = 'Gagal memuat detail lapangan';
                }
            },

            closeModal() {
                this.isOpen = false;
                this.resetForm();
            },

            resetForm() {
                this.availableTimeSlots = [];
                this.selectedSlots = [];
                this.selectedDate = null;
                this.selectedCourt = null;
            },

            async fetchSchedules() {
                try {
                    console.log('Fetching schedules for booking:', this.bookingId);
                    const url = new URL(`/user/bookings/${this.bookingId}/schedules`, window.location.origin);
                    if (this.selectedDate) {
                        url.searchParams.append('date', this.selectedDate);
                    }
                    
                    const response = await fetch(url);
                    const data = await response.json();
                    console.log('Received schedule data:', data);

                    if (!response.ok) {
                        throw new Error(data.message || 'HTTP error! status: ' + response.status);
                    }

                    if (!data.schedules || !Array.isArray(data.schedules)) {
                        throw new Error('Invalid schedule data received');
                    }

                    this.availableTimeSlots = data.schedules;
                    this.dates = data.dates;
                    this.selectedDate = data.selected_date;
                    
                    // Reset selected slots when changing date
                    this.selectedSlots = [];
            
                    this.errorMessage = null;
                } catch (error) {
                    console.error('Error fetching schedules:', error);
                    this.errorMessage = error.message || 'Gagal memuat jadwal. Silakan coba lagi.';
                    this.availableTimeSlots = [];
                    this.dates = [];
                }
            },

            async selectDate(date) {
                if (this.selectedDate === date) return;
                this.selectedDate = date;
                await this.fetchSchedules();
            },

            selectTimeSlot(slot) {
                if (!this.canSelectSlot(slot)) return;

                const index = this.selectedSlots.findIndex(s => s.time === slot.time);
                
                if (index > -1) {
                    // Jika slot sudah dipilih, hapus slot ini dan semua slot setelahnya
                    this.selectedSlots = this.selectedSlots.slice(0, index);
                } else {
                    // Jika slot belum dipilih
                    if (this.selectedSlots.length === 0) {
                        // Jika belum ada slot yang dipilih, tambahkan slot ini
                        this.selectedSlots = [slot];
                    } else {
                        // Cek apakah slot ini berurutan dengan slot terakhir yang dipilih
                        const lastSlot = this.selectedSlots[this.selectedSlots.length - 1];
                        const lastTime = new Date(`2000-01-01 ${lastSlot.time}`);
                        const currentTime = new Date(`2000-01-01 ${slot.time}`);
                        const diffHours = (currentTime - lastTime) / (1000 * 60 * 60);

                        if (diffHours === 1) {
                            // Jika slot berurutan, tambahkan ke selection
                            this.selectedSlots.push(slot);
                        } else {
                            // Jika tidak berurutan, mulai selection baru
                            this.selectedSlots = [slot];
                        }
                    }
                }
            },

            isSelected(slot) {
                return this.selectedSlots.some(s => s.time === slot.time);
            },

            canSelectSlot(slot) {
                if (slot.status !== 'available') return false;

                if (this.selectedSlots.length === 0) return true;

                const lastSlot = this.selectedSlots[this.selectedSlots.length - 1];
                const lastTime = new Date(`2000-01-01 ${lastSlot.time}`);
                const currentTime = new Date(`2000-01-01 ${slot.time}`);
                const diffHours = (currentTime - lastTime) / (1000 * 60 * 60);

                return diffHours === 1;
            },

            get canSubmit() {
                return this.selectedSlots.length > 0;
            },

            formatTime(time) {
                return time.substring(0, 5);
            },

            getStatusText(status) {
                switch(status) {
                    case 'holiday': return 'Libur';
                    case 'booked': return 'Sudah Dibooking';
                    case 'available': return 'Tersedia';
                    default: return '';
                }
            },

            getTimeSlotClasses(slot) {
                if (slot.status === 'booked' || slot.status === 'holiday') {
                    return 'bg-white text-gray-400';
                }
                
                if (this.isSelected(slot)) {
                    return 'bg-primary text-white shadow-lg shadow-primary/30 transform scale-105';
                }
                
                return 'bg-white text-gray-800';
            },

            async updateSchedule() {
                if (!this.canSubmit) return;

                try {
                    const response = await fetch(`/user/bookings/${this.bookingId}/schedule`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            slots: this.selectedSlots,
                            duration: this.selectedSlots.length,
                            date: this.selectedDate
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Gagal memperbarui jadwal');
                    }
                } catch (error) {
                    console.error('Error updating schedule:', error);
                    this.errorMessage = error.message || 'Terjadi kesalahan saat memperbarui jadwal';
                }
            }
        }));
    });

    // Inisialisasi Swiper
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.courtImageSlider', {
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            speed: 1000
        });
    });
    </script>
    @endpush
</x-app-layout>
