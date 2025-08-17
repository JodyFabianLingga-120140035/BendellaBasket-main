<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bendella Basket</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e98c37',
                        secondary: '#909c9b',
                        white: '#ffffff',
                    },
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tambahkan script untuk mengelola state modal -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', {
                login: false,
                register: false
            })
        })
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }

        .hover-scale {
            transition: transform 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FF6B35' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .text-gradient {
            background: linear-gradient(135deg, #e98c37 0%, #f4a261 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #e98c37 0%, #f4a261 100%);
        }

        .swiper-pagination-bullet-active {
            background: var(--primary) !important;
        }

        /* Custom Animations */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(.33);
            }

            80%,
            100% {
                opacity: 0;
            }
        }

        .animate-ping-slow {
            animation: pulse-ring 3s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            section {
                padding-top: 3rem;
                padding-bottom: 3rem;
            }

            .hero-text {
                font-size: 2rem;
                line-height: 1.2;
            }

            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .grid {
                gap: 1.5rem;
            }

            .card {
                margin-bottom: 1.5rem;
            }
        }

        /* Perbaiki overflow pada mobile */
        body {
            overflow-x: hidden;
        }

        /* Perbaiki padding container pada mobile */
        .max-w-7xl {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Perbaiki ukuran gambar pada mobile */
        @media (max-width: 768px) {
            .object-cover {
                height: 200px;
            }

            .rounded-3xl {
                border-radius: 1rem;
            }

            .shadow-2xl {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            }
        }
    </style>
</head>

<body class="font-sans antialiased bg-white" x-data="{ showLoginModal: false, showRegisterModal: false }">
    <!-- Navigation -->
    <nav class="fixed w-full z-[60] bg-white/95 backdrop-blur-sm shadow-sm" x-data="{ isOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="{{ asset('/images/logo/logo.png') }}" 
                        alt="Logo" 
                        class="h-10 w-10 md:h-16 md:w-16 object-contain">
                    <h1 class="ml-2 text-lg md:text-xl font-bold text-primary">
                        <span class="hidden md:inline">Bendella Basket</span>
                        <span class="inline md:hidden">Bendella</span>
                    </h1>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-secondary hover:text-primary transition">Beranda</a>
                    <a href="#facilities" class="text-secondary hover:text-primary transition">Fasilitas</a>
                    <a href="#gallery" class="text-secondary hover:text-primary transition">Galeri</a>
                    <a href="#courts" class="text-secondary hover:text-primary transition">Lapangan</a>
                    <a href="#contact" class="text-secondary hover:text-primary transition">Kontak</a>

                    @auth
                    <a href="{{ url('/dashboard') }}"
                        class="bg-primary text-white px-6 py-2 rounded-xl hover:opacity-90 transition">
                        Dashboard
                    </a>
                    @else
                    <button @click="$store.modal.login = true"
                        class="text-secondary hover:text-primary transition">
                        Masuk
                    </button>
                    <button @click="$store.modal.register = true"
                        class="bg-primary text-white px-6 py-2 rounded-xl hover:opacity-90 transition">
                        Daftar
                    </button>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button @click="isOpen = !isOpen" 
                        class="text-secondary hover:text-primary p-2 -mr-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="isOpen" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="md:hidden fixed left-0 right-0 top-16 bottom-0 bg-white/95 backdrop-blur-sm z-[55]" 
                x-cloak>
                <div class="h-full overflow-y-auto pb-36">
                    <div class="px-4 py-4 space-y-3">
                        <a href="#home"
                            @click="isOpen = false"
                            class="block px-4 py-3 rounded-xl text-base font-medium text-secondary hover:text-primary hover:bg-gray-50 transition">
                            Beranda
                        </a>
                        <a href="#facilities"
                            @click="isOpen = false"
                            class="block px-4 py-3 rounded-xl text-base font-medium text-secondary hover:text-primary hover:bg-gray-50 transition">
                            Fasilitas
                        </a>
                        <a href="#gallery"
                            @click="isOpen = false"
                            class="block px-4 py-3 rounded-xl text-base font-medium text-secondary hover:text-primary hover:bg-gray-50 transition">
                            Galeri
                        </a>
                        <a href="#courts"
                            @click="isOpen = false"
                            class="block px-4 py-3 rounded-xl text-base font-medium text-secondary hover:text-primary hover:bg-gray-50 transition">
                            Lapangan
                        </a>
                        <a href="#contact"
                            @click="isOpen = false"
                            class="block px-4 py-3 rounded-xl text-base font-medium text-secondary hover:text-primary hover:bg-gray-50 transition">
                            Kontak
                        </a>

                        @auth
                        <a href="{{ url('/dashboard') }}"
                            @click="isOpen = false"
                            class="block px-4 py-3 rounded-xl text-base font-medium text-primary hover:text-primary/80 hover:bg-gray-50 transition">
                            Dashboard
                        </a>
                        @else
                        <div class="mt-6 space-y-3 px-4">
                            <button @click="$store.modal.login = true; isOpen = false"
                                class="w-full px-6 py-3 text-base font-medium text-secondary border border-gray-200 hover:text-primary hover:border-primary rounded-xl transition">
                                Masuk
                            </button>
                            <button @click="$store.modal.register = true; isOpen = false"
                                class="w-full px-6 py-3 text-base font-medium text-white bg-primary hover:bg-primary/90 rounded-xl transition">
                                Daftar
                            </button>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center pt-20 overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-1/2 -right-1/2 w-[1000px] h-[1000px] rounded-full bg-primary/5"></div>
            <div class="absolute -bottom-1/2 -left-1/2 w-[1000px] h-[1000px] rounded-full bg-primary/5"></div>
        </div>

        <div class="relative w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Gambar Besar Kiri -->
                <div class="md:col-span-8" data-aos="fade-right">
                    <div class="relative overflow-hidden rounded-3xl shadow-2xl h-[600px]">
                        <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-transparent"></div>
                        <img src="{{ asset('images/lay/2.jpg') }}"
                            alt="Basketball Court Main"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                    </div>
                </div>

                <!-- Gambar Kecil Kanan -->
                <div class="md:col-span-4 flex flex-col gap-6" data-aos="fade-left">
                    <div class="overflow-hidden rounded-2xl shadow-lg h-[290px]">
                        <img src="{{ asset('images/lay/1.jpg') }}"
                            alt="Basketball Activities 1"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="overflow-hidden rounded-2xl shadow-lg h-[290px]">
                        <img src="{{ asset('images/lay/3.jpg') }}"
                            alt="Basketball Activities 2"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                    </div>
                </div>

                <!-- Text Content di Bawah -->
                <div class="md:col-span-12 text-center mt-8" data-aos="fade-up">
                    <h1 class="text-4xl md:text-6xl font-bold text-secondary leading-tight mb-6">
                        Champion Start Here <br>
                        <span class="text-primary">Proven Way Become a Global Player</span><br>
                    </h1>
                    <p class="text-lg text-secondary/80 mb-4 max-w-3xl mx-auto">
                        Nikmati pengalaman bermain basket di lapangan berstandar FIBA dan lapangan indoor
                        terbaik di Kota Bekasi, dengan fasilitas yang lengkap dan harga terjangkau.
                    </p>
                    <!-- Tambahkan keterangan harga -->
                    <p class="text-2xl font-bold text-primary mb-8">
                        Mulai dari <span class="text-3xl">Rp 300.000</span><span class="text-lg text-secondary/80">/jam</span>
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                        <a href="{{ route('user.courts.index') }}"
                            class="bg-primary text-white px-8 py-4 rounded-xl hover:opacity-90 transition text-center">
                            Booking Sekarang
                        </a>
                        @else
                        <button @click="$store.modal.register = true"
                            class="bg-primary text-white px-8 py-4 rounded-xl hover:opacity-90 transition">
                            Daftar & Booking
                        </button>
                        @endauth
                        <a href="#courts"
                            class="border-2 border-primary text-primary px-8 py-4 rounded-xl hover:bg-primary hover:text-white transition text-center">
                            Lihat Lapangan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="facilities" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-secondary mb-4">Fasilitas Unggulan</h2>
                <p class="text-secondary/80">Nikmati berbagai fasilitas premium untuk pengalaman bermain terbaik</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-primary/10 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4">Lapangan Premium</h3>
                    <p class="text-secondary/80">Lapangan Basket indoor berstandar Fiba dengan material vinyl premium</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-primary/10 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4">Fasilitas Lengkap</h3>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-secondary/80">Café & Resto</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-secondary/80">Parkiran Motor & Mobil</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-secondary/80">Scoreboard & Shot Clock</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-secondary/80">Tribun & Kursi Penonton</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-secondary/80">Musholla</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-secondary/80">Toilet & Shower</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-secondary/80">Wi-Fi</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg hover-scale" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-primary/10 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-4">Pembayaran Mudah</h3>
                    <p class="text-secondary/80">Berbagai metode pembayaran tersedia untuk kenyamanan Anda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Courts Slider Section -->
    <section id="gallery" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-secondary mb-4">Galeri Lapangan</h2>
                <p class="text-secondary/80">Lihat keunggulan fasilitas lapangan kami</p>
            </div>

            <div class="swiper courtSlider">
                <div class="swiper-wrapper">
                    @foreach($courts as $court)
                    @php
                    $photos = explode(',', $court->photo);
                    @endphp
                    @foreach($photos as $photo)
                    <div class="swiper-slide">
                        <div class="relative rounded-2xl overflow-hidden aspect-video">
                            <img src="{{ asset($photo) }}"
                                alt="{{ $court->name }}"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 p-6 text-white">
                                <h3 class="text-2xl font-bold mb-2">{{ $court->name }}</h3>
                                <p class="text-white/90">{{ $court->description }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- Courts Section -->
    <section id="courts" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-secondary mb-4">Lapangan Kami</h2>
                <p class="text-secondary/80">Pilih lapangan terbaik sesuai kebutuhan Anda</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($courts as $court)
                @php
                $photos = explode(',', $court->photo);
                $mainPhoto = $photos[0] ?? ''; // Ambil foto pertama sebagai foto utama
                @endphp
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover-scale"
                    data-aos="fade-up"
                    data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="aspect-video">
                        <img src="{{ asset($court->photo) }}"
                            alt="{{ $court->name }}"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-secondary mb-2">{{ $court->name }}</h3>
                        <p class="text-secondary/80 mb-4">{{ $court->description }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-primary font-bold">
                                Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}/jam
                            </span>
                            @auth
                            <a href="{{ route('user.courts.show', $court) }}"
                                class="text-primary hover:text-opacity-80 flex items-center">
                                Booking
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            @else
                            <button @click="$store.modal.register = true"
                                class="text-primary hover:text-opacity-80 flex items-center">
                                Daftar & Booking
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-20 bg-primary overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-1/2 -right-1/2 w-[1000px] h-[1000px] rounded-full bg-white/5"></div>
            <div class="absolute -bottom-1/2 -left-1/2 w-[1000px] h-[1000px] rounded-full bg-white/5"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Siap Untuk Bermain?</h2>
            <p class="text-white/90 mb-8 max-w-2xl mx-auto">
                Booking lapangan sekarang dan nikmati pengalaman bermain basket terbaik
            </p>
            @auth
            <a href="{{ route('user.courts.index') }}"
                class="inline-block bg-white text-primary px-8 py-4 rounded-xl hover:bg-opacity-90 transition">
                Booking Sekarang
            </a>
            @else
            <button @click="$store.modal.register = true"
                class="inline-block bg-white text-primary px-8 py-4 rounded-xl hover:bg-opacity-90 transition">
                Daftar & Booking
            </button>
            @endauth
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-secondary mb-4">Kontak & Lokasi</h2>
                <p class="text-secondary/80">Temukan kami di sosial media atau kunjungi lokasi kami</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Social Media -->
                <div class="bg-white p-8 rounded-2xl shadow-lg" data-aos="fade-right">
                    <h3 class="text-2xl font-bold text-secondary mb-6">Sosial Media</h3>
                    <div class="space-y-6">
                        <a href="https://www.instagram.com/bendellabasket?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="flex items-center space-x-4 text-secondary hover:text-primary transition">
                            <div class="bg-primary/10 w-12 h-12 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold">Instagram</p>
                                <p class="text-sm text-secondary/80">@bendellabasket</p>
                            </div>
                        </a>

                        <a href="https://x.com/bendellabasket" class="flex items-center space-x-4 text-secondary hover:text-primary transition">
                            <div class="bg-primary/10 w-12 h-12 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold">X</p>
                                <p class="text-sm text-secondary/80">@bendellabasket</p>
                            </div>
                        </a>

                        <a href="https://www.youtube.com/@bendellabasket" class="flex items-center space-x-4 text-secondary hover:text-primary transition">
                            <div class="bg-primary/10 w-12 h-12 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold">YouTube</p>
                                <p class="text-sm text-secondary/80">@bendellabasket</p>
                            </div>
                        </a>

                        <a href="https://www.tiktok.com/@bendellabasket?is_from_webapp=1&sender_device=pc" class="flex items-center space-x-4 text-secondary hover:text-primary transition">
                            <div class="bg-primary/10 w-12 h-12 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold">TikTok</p>
                                <p class="text-sm text-secondary/80">bendellabasket</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Location Map -->
                <div data-aos="fade-left">
                    <div class="bg-white p-2 rounded-2xl shadow-lg mb-6">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.4726814506547!2d107.00586497488771!3d-6.224397993755676!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTMnMjcuOCJTIDEwN8KwMDAnMjkuMCJF!5e0!3m2!1sid!2sid!4v1644048041234!5m2!1sid!2sid"
                            width="100%"
                            height="300"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            class="rounded-xl">
                        </iframe>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg">
                        <h3 class="text-xl font-bold text-secondary mb-4">Lokasi Kami</h3>
                        <p class="text-secondary/80">
                            Jl. Raya Perjuangan No.26, Marga Mulya,<br />
                            Kec. Bekasi Utara, Kota Bks, Jawa Barat<br />
                            17124
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Modals -->
    @include('auth.modal-login')
    @include('auth.modal-register')

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });

        // Initialize Swiper
        const courtSlider = new Swiper('.courtSlider', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });
    </script>

    <!-- Floating Buttons -->
    <div class="fixed bottom-4 md:bottom-8 right-4 md:right-8 z-[50] flex flex-col items-end space-y-4">
        <!-- Message Box -->
        <div class="bg-white text-secondary px-4 md:px-6 py-3 md:py-4 rounded-2xl shadow-lg text-sm max-w-[250px] md:max-w-xs transform hover:scale-105 transition-all duration-300 hidden md:block"
            style="animation: float 3s ease-in-out infinite;">
            <div class="flex items-start space-x-3">
                <div class="bg-primary/10 p-2 rounded-lg">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-sm md:text-base text-primary mb-1">Order via WhatsApp 👋</p>
                    <p class="text-secondary/80 text-xs md:text-sm">Hubungi kami untuk Menjadi Member!</p>
                </div>
            </div>
        </div>

        <!-- WhatsApp Button -->
        <a href="https://wa.me/6282211222526"
            target="_blank"
            class="bg-[#25D366] text-white p-3 md:p-4 rounded-full shadow-lg hover:scale-110 transition-all duration-300 group relative">
            <!-- Pulse Effect -->
            <div class="absolute inset-0 rounded-full bg-[#25D366] animate-ping opacity-25"></div>

            <!-- WhatsApp Icon -->
            <svg class="w-5 h-5 md:w-6 md:h-6 relative" fill="currentColor" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
            </svg>

            <!-- Tooltip -->
            <span class="absolute right-full mr-4 bg-black/80 backdrop-blur-sm text-white text-xs md:text-sm py-2 px-4 rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap hidden md:block">
                Chat WhatsApp
            </span>
        </a>
    </div>

    <!-- Mobile Floating Button (Simplified Version) -->
    <div class="fixed bottom-4 right-4 z-[50] md:hidden">
        <a href="https://wa.me/6281234567890"
            target="_blank"
            class="flex items-center space-x-2 bg-[#25D366] text-white px-4 py-3 rounded-full shadow-lg hover:scale-105 transition-all duration-300">
            <!-- Pulse Effect -->
            <div class="absolute inset-0 rounded-full bg-[#25D366] animate-ping opacity-25"></div>

            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
            </svg>
            <span class="text-base font-medium">Chat Sekarang</span>
        </a>
    </div>
</body>

</html>