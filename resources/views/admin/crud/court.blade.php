@extends('admin.index')

@section('main')
<div class="p-4 bg-white dark:bg-dark-bg-secondary rounded-lg shadow-sm mb-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Lapangan</h2>
        <div class="flex gap-2">
            <button onclick="openModal('holidayModal')" 
                    class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Atur Libur
            </button>
            <button onclick="openModal('addCourtModal')" 
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Lapangan
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-300" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Courts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courts as $court)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <!-- Image Slideshow -->
                <div x-data="imageSlider('{{ $court->photo }}')" class="relative h-48 bg-gray-200">
                    <!-- Images -->
                    <template x-for="(image, index) in images" :key="index">
                        <div x-show="currentIndex === index" 
                             class="absolute inset-0 w-full h-full transition-opacity duration-500"
                             :class="{'opacity-100': currentIndex === index, 'opacity-0': currentIndex !== index}">
                            <img :src="'/' + image" 
                                 :alt="'{{ $court->name }} - Image ' + (index + 1)"
                                 class="w-full h-full object-cover">
                        </div>
                    </template>

                    <!-- Navigation Arrows -->
                    <template x-if="images.length > 1">
                        <div>
                            <!-- Previous Button -->
                            <button @click="prev()" 
                                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full hover:bg-black/75 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <!-- Next Button -->
                            <button @click="next()" 
                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full hover:bg-black/75 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <!-- Indicators -->
                    <template x-if="images.length > 1">
                        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex space-x-2">
                            <template x-for="(image, index) in images" :key="index">
                                <button @click="currentIndex = index"
                                        class="w-2 h-2 rounded-full transition-colors"
                                        :class="currentIndex === index ? 'bg-white' : 'bg-white/50'">
                                </button>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Court Info -->
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $court->name }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $court->location }}
                    </p>
                    <p class="text-primary font-semibold mb-4">
                        Rp {{ number_format($court->price_per_hour, 0, ',', '.') }}/jam
                    </p>
                    <div class="flex justify-between items-center">
                        <button onclick="openEditModal({{ $court->id }}, '{{ $court->name }}', '{{ $court->location }}', '{{ $court->description }}', {{ $court->price_per_hour }}, {{ $court->is_available ? 'true' : 'false' }})" 
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form action="{{ route('admin.court.destroy', $court) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus lapangan ini?')"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @if($court->isHoliday())
                        <div class="absolute top-2 right-2 px-2 py-1 bg-yellow-500 text-white text-sm rounded-full">
                            Libur
                        </div>
                    @endif
                    @if($court->status === 'inactive')
                        <div class="absolute top-2 left-2 px-2 py-1 bg-red-500 text-white text-sm rounded-full">
                            Tidak Aktif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Add Court Modal -->
<div id="addCourtModal" class="fixed inset-0 z-50 hidden overflow-y-auto" x-data="{ loading: false }">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full transform transition-all">
            <div class="absolute top-3 right-3">
                <button onclick="closeModal('addCourtModal')" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-4">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Tambah Lapangan Baru</h3>
                </div>
                <form action="{{ route('admin.court.store') }}" method="POST" enctype="multipart/form-data" @submit="loading = true">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lapangan</label>
                            <input type="text" name="name" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
                            <input type="text" name="location" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <textarea name="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga per Jam</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="price_per_hour" required
                                       class="pl-12 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto Lapangan (Maks. 20)</label>
                            <input type="file" name="photos[]" required accept="image/*" multiple
                                   class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-primary file:text-white
                                          hover:file:bg-primary/90"
                                   onchange="validateFiles(this)">
                            <p class="mt-1 text-sm text-gray-500">
                                PNG, JPG, JPEG (Maks. 20 foto, total ukuran maksimal 50MB)
                            </p>
                        </div>
                        @error('photos')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        @error('photos.*')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('addCourtModal')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors flex items-center gap-2"
                                :class="{ 'opacity-50 cursor-not-allowed': loading }"
                                :disabled="loading">
                            <span x-show="!loading">Simpan</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Court Modal -->
<div id="editCourtModal" class="fixed inset-0 z-50 hidden overflow-y-auto" x-data="{ loading: false }">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full transform transition-all">
            <div class="absolute top-3 right-3">
                <button type="button" onclick="closeModal('editCourtModal')" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Lapangan</h3>
                </div>
                <form id="editCourtForm" method="POST" enctype="multipart/form-data" @submit="loading = true">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lapangan</label>
                            <input type="text" name="name" id="editName" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
                            <input type="text" name="location" id="editLocation" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <textarea name="description" id="editDescription" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga per Jam</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="price_per_hour" id="editPrice" required
                                       class="pl-12 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto Lapangan (Maks. 20)</label>
                            <input type="file" name="photos[]" accept="image/*" multiple
                                   class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-primary file:text-white
                                          hover:file:bg-primary/90"
                                   onchange="validateFiles(this)">
                            <p class="mt-1 text-sm text-gray-500">PNG, JPG, JPEG (Maks. 20 foto, total ukuran maksimal 50MB)</p>
                        </div>
                        @error('photos')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        @error('photos.*')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <div class="flex items-center">
                            <input type="checkbox" name="is_available" id="editIsAvailable"
                                   class="rounded border-gray-300 text-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600">
                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Tersedia</label>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('editCourtModal')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors flex items-center gap-2"
                                :class="{ 'opacity-50 cursor-not-allowed': loading }"
                                :disabled="loading">
                            <span x-show="!loading">Update</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan Holiday Modal -->
<div id="holidayModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full transform transition-all">
            <div class="absolute top-3 right-3">
                <button onclick="closeModal('holidayModal')" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-5">Atur Tanggal Libur</h3>
                
                <!-- Form Tambah Libur -->
                <form action="{{ route('admin.court.setHoliday') }}" method="POST" class="mb-6">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Pilih Tanggal Libur
                            </label>
                            <input type="date" name="holiday_date" required min="{{ date('Y-m-d') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Waktu Mulai
                                </label>
                                <input type="time" name="holiday_start_time" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Waktu Selesai
                                </label>
                                <input type="time" name="holiday_end_time" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="w-full mt-4 px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors">
                        Tambah Tanggal Libur
                    </button>
                </form>

                <!-- Daftar Tanggal Libur -->
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">Daftar Tanggal Libur</h4>
                    <div class="space-y-2">
                        @foreach(App\Models\BasketCourt::first()?->holiday_dates ?? [] as $date)
                            <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                    </span>
                                    <br>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ App\Models\BasketCourt::first()->holiday_start_time->format('H:i') }} - 
                                        {{ App\Models\BasketCourt::first()->holiday_end_time->format('H:i') }}
                                    </span>
                                </div>
                                <form action="{{ route('admin.court.removeHoliday') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="holiday_date" value="{{ $date }}">
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function openEditModal(id, name, location, description, price, isAvailable) {
    const form = document.getElementById('editCourtForm');
    form.action = `/admin/court/${id}`;
    
    document.getElementById('editName').value = name;
    document.getElementById('editLocation').value = location;
    document.getElementById('editDescription').value = description;
    document.getElementById('editPrice').value = price;
    document.getElementById('editIsAvailable').checked = isAvailable;
    
    openModal('editCourtModal');
}

function validateFiles(input) {
    // Validasi jumlah file
    if (input.files.length > 20) {
        alert('Maksimal 20 foto yang diperbolehkan');
        input.value = '';
        return;
    }

    // Validasi total ukuran file
    let totalSize = 0;
    for (let i = 0; i < input.files.length; i++) {
        totalSize += input.files[i].size;
    }

    // Convert to MB for comparison (50MB = 52428800 bytes)
    const totalSizeMB = totalSize / (1024 * 1024);
    if (totalSizeMB > 50) {
        alert('Total ukuran foto tidak boleh melebihi 50MB');
        input.value = '';
        return;
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('imageSlider', (imageString) => ({
        images: imageString ? imageString.split(',').filter(Boolean) : [],
        currentIndex: 0,
        timer: null,

        init() {
            if (this.images.length > 1) {
                this.startSlideshow();
            }
        },

        startSlideshow() {
            this.timer = setInterval(() => {
                this.next();
            }, 5000); // Ganti slide setiap 5 detik
        },

        stopSlideshow() {
            if (this.timer) {
                clearInterval(this.timer);
            }
        },

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },

        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        }
    }));
});
</script>
@endsection