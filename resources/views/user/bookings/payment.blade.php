<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-secondary leading-tight">
            {{ __('Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if (session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 p-4 rounded-lg shadow-md transform hover:-translate-y-1 transition-all duration-300" role="alert">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-red-700">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Content -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <!-- Informasi Booking -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Detail Booking</h3>
                        <div class="grid gap-4">
                            @foreach($bookings as $booking)
                                <div class="bg-white rounded-xl shadow-md hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] 
                                            border border-gray-100 p-6 transform hover:-translate-y-1 transition-all duration-300">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-800 mb-1">{{ $booking->court->name }}</h4>
                                            <p class="text-gray-600 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                </svg>
                                                {{ $booking->court->location }}
                                            </p>
                                            @if($booking->schedule)
                                                <div class="mt-4 space-y-1">
                                                    <div class="flex items-center gap-2 text-gray-600">
                                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        {{ $booking->schedule->schedule_date->format('d M Y') }}
                                                    </div>
                                                    <div class="flex items-center gap-2 text-gray-600">
                                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $booking->schedule->start_time->format('H:i') }} - 
                                                        {{ $booking->schedule->end_time->format('H:i') }} WIB
                                                        ({{ $booking->duration }} Jam)
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent">
                                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form action="{{ route('user.bookings.process-payment') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-8">
                        @csrf

                        <!-- Metode Pembayaran -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Metode Pembayaran</h3>
                            <div x-data="{ selectedMethod: null }" 
                                 class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($paymentMethods as $method)
                                    <label :class="{ 'ring-2 ring-primary transform -translate-y-1': selectedMethod === {{ $method->id }} }"
                                           class="relative bg-white rounded-xl shadow-md 
                                                  hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100 
                                                  p-6 cursor-pointer transition-all duration-300">
                                        <input type="radio"
                                               name="payment_method_id"
                                               value="{{ $method->id }}"
                                               class="absolute inset-0 opacity-0 cursor-pointer"
                                               @change="selectedMethod = {{ $method->id }}; showQris('{{ $method->qris_img }}')"
                                               required>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-lg font-bold text-gray-800">{{ $method->name }}</p>
                                                @if($method->account_number)
                                                    <p class="text-gray-600">{{ $method->account_number }}</p>
                                                    <p class="text-gray-600">a.n {{ $method->account_name }}</p>
                                                @endif
                                            </div>
                                            <div class="w-12 h-12 flex items-center justify-center rounded-full
                                                        bg-gradient-to-br from-primary/10 to-transparent">
                                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- QRIS Image Container -->
                        <div id="qrisContainer" class="hidden">
                            <div class="bg-white rounded-xl shadow-md hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] p-6 
                                        transform hover:-translate-y-1 transition-all duration-300">
                                <img id="qrisImage" src="" alt="QRIS Code" class="mx-auto max-w-xs">
                            </div>
                        </div>

                        <!-- Jenis Pembayaran -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Jenis Pembayaran</h3>
                            <div x-data="{ selectedPayment: null }" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label :class="{ 'ring-2 ring-primary transform -translate-y-1': selectedPayment === 'full_payment' }"
                                       class="relative bg-white rounded-xl shadow-md 
                                              hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100 
                                              p-6 cursor-pointer transition-all duration-300">
                                    <input type="radio"
                                           name="payment_type"
                                           value="full_payment"
                                           class="absolute inset-0 opacity-0 cursor-pointer"
                                           @change="selectedPayment = 'full_payment'"
                                           required>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-lg font-bold text-gray-800">Bayar Penuh</p>
                                            <p class="text-2xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent mt-2">
                                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="w-12 h-12 flex items-center justify-center rounded-full
                                                    bg-gradient-to-br from-primary/10 to-transparent">
                                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>

                                <label :class="{ 'ring-2 ring-primary transform -translate-y-1': selectedPayment === 'down_payment' }"
                                       class="relative bg-white rounded-xl shadow-md 
                                              hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100 
                                              p-6 cursor-pointer transition-all duration-300">
                                    <input type="radio"
                                           name="payment_type"
                                           value="down_payment"
                                           class="absolute inset-0 opacity-0 cursor-pointer"
                                           @change="selectedPayment = 'down_payment'"
                                           required>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-lg font-bold text-gray-800">Down Payment (50%)</p>
                                            <p class="text-2xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent mt-2">
                                                Rp {{ number_format($downPaymentAmount, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="w-12 h-12 flex items-center justify-center rounded-full
                                                    bg-gradient-to-br from-primary/10 to-transparent">
                                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Upload Bukti Transfer -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Bukti Pembayaran</h3>
                            <div class="bg-white rounded-xl shadow-md 
                                        hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100 
                                        border-dashed p-8 transition-all duration-300">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 flex items-center justify-center rounded-full
                                                bg-gradient-to-br from-primary/10 to-transparent mb-4">
                                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-800 mb-2">Upload Bukti Transfer</p>
                                    <p class="text-sm text-gray-600 mb-4">Format: JPG, PNG, JPEG. Maksimal 2MB</p>
                                    <input type="file"
                                           name="payment_proof"
                                           accept="image/*"
                                           required
                                           class="block w-full text-sm text-gray-600
                                                  file:mr-4 file:py-2 file:px-4 file:rounded-full
                                                  file:border-0 file:text-sm file:font-medium
                                                  file:bg-primary/10 file:text-primary
                                                  hover:file:bg-primary/20 transition-all
                                                  cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="border-t pt-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Syarat dan Ketentuan</h3>
                            <div class="bg-white rounded-xl shadow-md hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] p-6 mb-6
                                        transition-all duration-300">
                                <div class="bg-gray-50 rounded-lg p-6 max-h-[400px] overflow-y-auto custom-scrollbar">
                                    <h4 class="font-bold text-gray-800 mb-4">Peraturan Penggunaan Lapangan:</h4>
                                    <div class="space-y-4 text-gray-600">
                                        <!-- Peralatan dan Sepatu -->
                                        <div class="space-y-2">
                                            <h5 class="font-semibold text-primary">Peralatan dan Sepatu</h5>
                                            <ol class="list-decimal pl-4 space-y-2">
                                                <li>Wajib menggunakan alas karpet yang telah disediakan untuk Ring Portable dan Peralatan Latihan Berat lainnya.</li>
                                                <li>Wajib memakai sepatu basket saat berada di dalam lapangan.</li>
                                                <li>Wajib menggunakan Sepatu setelah berada di pinggir lapangan. (Tidak diperkenankan memakai dari luar lapangan).</li>
                                                <li>Sandal, sepatu kerja, sepatu yang memiliki heels, atau sepatu apapun selain sepatu basket dilarang masuk ke dalam lapangan.</li>
                                                <li>Pemain wajib membersihkan sepatu yang digunakan sebelum masuk ke dalam lapangan.</li>
                                                <li>Pemain wajib memastikan sepatu yang digunakan bersih dari debu, kerikil, batu dan benda lainnya sebelum masuk ke dalam area lapangan.</li>
                                            </ol>
                                        </div>

                                        <!-- Makanan dan Kebersihan -->
                                        <div class="space-y-2">
                                            <h5 class="font-semibold text-primary">Makanan dan Kebersihan</h5>
                                            <ol class="list-decimal pl-4 space-y-2" start="7">
                                                <li>Dilarang membawa makanan dan minuman dari luar.</li>
                                                <li>Dilarang makan di dalam area lapangan.</li>
                                                <li>Wajib menjaga ketertiban dan kebersihan di seluruh area Bendella Basket.</li>
                                                <li>Dilarang membuang sampah dan meludah sembarangan.</li>
                                            </ol>
                                        </div>

                                        <!-- Waktu dan Dokumentasi -->
                                        <div class="space-y-2">
                                            <h5 class="font-semibold text-primary">Waktu dan Dokumentasi</h5>
                                            <ol class="list-decimal pl-4 space-y-2" start="11">
                                                <li>Seluruh kegiatan di Bendella Basket dapat didokumentasikan sebagai konten sosial media kami.</li>
                                                <li>Pemesan bertanggung jawab untuk mengganti jika ada kerusakan fasilitas yang disebabkan oleh pemesan dan rombongan pemain/pengunjung.</li>
                                                <li>Jam pemakaian lapangan berlaku sesuai jam pemesanan. Pemain diharapkan masuk dan keluar lapangan sesuai waktu pemesanan.</li>
                                                <li>Pengunjung disarankan untuk membawa bola/peralatan sendiri sesuai olahraga yang dilakukan.</li>
                                                <li>Ketika waktu bermain habis, pemain wajib keluar dari lapangan karena jam berikutnya adalah hak bagi pemesan berikutnya.</li>
                                            </ol>
                                        </div>

                                        <!-- Larangan Umum -->
                                        <div class="space-y-2">
                                            <h5 class="font-semibold text-primary">Larangan Umum</h5>
                                            <ol class="list-decimal pl-4 space-y-2" start="16">
                                                <li>Dilarang merokok, kecuali di area yang telah disediakan.</li>
                                                <li>Dilarang membawa hewan peliharaan ke dalam area Bendella Basket.</li>
                                                <li>Dilarang membawa senjata tajam, senjata api, obat-obatan terlarang dan barang illegal lainnya.</li>
                                                <li>Dilarang membawa bahan-bahan kimia yang mudah terbakar/meledak.</li>
                                                <li>Dilarang memindahkan fasilitas serta perlengkapan tanpa seizin pengelola.</li>
                                                <li>Dilarang memasang spanduk, atribut dan benda lainnya tanpa izin.</li>
                                            </ol>
                                        </div>

                                        <!-- Tanggung Jawab -->
                                        <div class="space-y-2">
                                            <h5 class="font-semibold text-primary">Tanggung Jawab</h5>
                                            <ol class="list-decimal pl-4 space-y-2" start="22">
                                                <li>Bendella Basket tidak bertanggung jawab atas kehilangan barang berharga dan cedera akibat kecelakaan di lapangan.</li>
                                                <li>Wajib mematuhi dan mengikuti aturan diatas.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-start gap-3">
                                    <input type="checkbox" 
                                           name="terms_accepted" 
                                           id="terms_accepted" 
                                           class="mt-1 w-4 h-4 text-primary border-gray-300 rounded
                                                  focus:ring-primary cursor-pointer" 
                                           onchange="toggleSubmitButton()"
                                           required>
                                    <label for="terms_accepted" class="text-gray-600">
                                        Saya telah membaca dan menyetujui syarat dan ketentuan yang berlaku
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- reCAPTCHA -->
                        <div class="flex justify-center">
                            <div class="bg-white rounded-xl shadow-md hover:shadow-[0_8px_20px_-4px_rgba(0,0,0,0.1)] p-6
                                        transition-all duration-300">
                                <div class="g-recaptcha" 
                                     data-sitekey="{{ config('services.recaptcha.site_key') }}"
                                     data-callback="onRecaptchaSuccess"
                                     data-expired-callback="onRecaptchaExpired"
                                     data-error-callback="onRecaptchaError">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="border-t pt-6">
                            <button type="submit"
                                    id="submitPayment"
                                    disabled
                                    class="w-full md:w-auto px-8 py-4 bg-gradient-to-r from-primary to-primary/90 
                                           text-white rounded-xl transition-all duration-300 transform hover:scale-105
                                           hover:shadow-lg hover:shadow-primary/30 disabled:opacity-50 
                                           disabled:cursor-not-allowed disabled:transform-none">
                                Proses Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan script reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <script>
        let isTermsAccepted = false;
        let isRecaptchaVerified = false;

        function toggleSubmitButton() {
            const checkbox = document.getElementById('terms_accepted');
            const submitButton = document.getElementById('submitPayment');
            
            isTermsAccepted = checkbox.checked;
            
            // Enable tombol hanya jika terms disetujui DAN recaptcha terverifikasi
            submitButton.disabled = !(isTermsAccepted && isRecaptchaVerified);
            
            updateButtonStyle(submitButton);
        }

        // Callback ketika reCAPTCHA berhasil
        function onRecaptchaSuccess(token) {
            console.log('reCAPTCHA Success - Token Length:', token.length);
            isRecaptchaVerified = true;
            const submitButton = document.getElementById('submitPayment');
            submitButton.disabled = !isTermsAccepted;
            updateButtonStyle(submitButton);
        }
        
        // Callback ketika reCAPTCHA expired
        function onRecaptchaExpired() {
            console.log('reCAPTCHA Expired');
            isRecaptchaVerified = false;
            const submitButton = document.getElementById('submitPayment');
            submitButton.disabled = true;
            updateButtonStyle(submitButton);
            grecaptcha.reset();
        }
        
        // Callback ketika reCAPTCHA error
        function onRecaptchaError(error) {
            console.error('reCAPTCHA Error:', error);
            isRecaptchaVerified = false;
            const submitButton = document.getElementById('submitPayment');
            submitButton.disabled = true;
            updateButtonStyle(submitButton);
        }

        // Update form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const recaptchaResponse = grecaptcha.getResponse();
            console.log('Form Submit - reCAPTCHA Response Length:', recaptchaResponse.length);
            
            if (!recaptchaResponse) {
                e.preventDefault();
                alert('Mohon verifikasi reCAPTCHA terlebih dahulu');
                return;
            }

            // Log form data sebelum submit
            const formData = new FormData(this);
            console.log('Form Data:', {
                payment_method: formData.get('payment_method_id'),
                payment_type: formData.get('payment_type'),
                terms_accepted: formData.get('terms_accepted'),
                recaptcha: formData.get('g-recaptcha-response')?.length
            });
        });

        function updateButtonStyle(button) {
            if (button.disabled) {
                button.classList.add('opacity-50', 'cursor-not-allowed');
                button.classList.remove('hover:bg-primary/90');
            } else {
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                button.classList.add('hover:bg-primary/90');
            }
        }

        // Fungsi untuk reset reCAPTCHA
        function resetRecaptcha() {
            grecaptcha.reset();
            disableSubmitButton();
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleSubmitButton();
        });

        // Existing showQris function
        function showQris(qrisImg) {
            const qrisContainer = document.getElementById('qrisContainer');
            const qrisImage = document.getElementById('qrisImage');
            
            if (qrisImg) {
                qrisImage.src = '/storage/' + qrisImg;
                qrisContainer.classList.remove('hidden');
            } else {
                qrisContainer.classList.add('hidden');
            }
        }
    </script>

    <!-- Custom Scrollbar Style -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #ccc;
        }
    </style>
</x-app-layout>