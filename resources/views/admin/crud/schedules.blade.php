@extends('admin.index')

@section('main')
<div class="p-4 bg-gray-900 text-white min-h-screen">
    <!-- Header dengan Navigasi dan Bulan -->
    <div class="flex justify-between items-center mb-4">
        <button class="p-2" onclick="changeWeek(-1)">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <h2 class="text-lg font-semibold">{{ $currentDate->format('F Y') }}</h2>
        <button class="p-2" onclick="changeWeek(1)">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <!-- Tab Lapangan -->
    <div class="flex gap-2 mb-4">
        @foreach($courts as $court)
        <button onclick="selectCourt({{ $court->id }})" 
                class="court-tab px-4 py-2 rounded-lg text-sm"
                data-court-id="{{ $court->id }}">
            {{ $court->name }}
        </button>
        @endforeach
    </div>

    <!-- Calendar Grid -->
    <div class="overflow-y-auto max-h-[calc(100vh-200px)]">
        <div class="grid grid-cols-8 gap-[1px] bg-gray-700">
            <!-- Time Column -->
            <div class="bg-gray-900">
                <div class="h-8"></div> <!-- Header spacer -->
                @for($hour = 6; $hour <= 23; $hour++)
                <div class="h-16 flex items-start pt-2 pl-2 text-sm text-gray-400">
                    {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00
                </div>
                @endfor
            </div>

            <!-- Days Columns -->
            @php
                $weekStart = $currentDate->copy()->startOfWeek();
                $days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            @endphp
            @for($i = 0; $i < 7; $i++)
            <div class="bg-gray-900">
                <!-- Day Header -->
                <div class="h-8 flex flex-col justify-center items-center text-sm border-b border-gray-700 {{ $weekStart->isToday() ? 'bg-orange-500' : '' }}">
                    <div class="text-xs text-gray-400">{{ $days[$i] }}</div>
                    <div class="font-medium {{ $weekStart->isToday() ? 'text-white' : 'text-gray-300' }}">
                        {{ $weekStart->format('j') }}
                    </div>
                </div>
                <!-- Time Slots -->
                @for($hour = 6; $hour <= 23; $hour++)
                <div class="h-16 border-b border-gray-700 relative">
                    @foreach($schedules as $schedule)
                    @if(date('Y-m-d', strtotime($schedule['schedule_date'])) == $weekStart->format('Y-m-d') && 
                        date('H', strtotime($schedule['start_time'])) == $hour)
                    <div class="absolute inset-x-0 p-1 text-xs schedule-item cursor-pointer hover:opacity-75"
                        data-court-id="{{ $schedule['court_id'] }}"
                        onclick='openEditModal(@json($schedule))'
                        style="display: none;
                        @if($schedule['status'] === 'available')
                            background-color: rgba(59, 130, 246, 0.3); /* Biru */
                            color: rgb(219, 234, 254);
                        @elseif($schedule['status'] === 'booked')
                            background-color: rgba(245, 158, 11, 0.3); /* Kuning */
                            color: rgb(254, 243, 199);
                        @elseif($schedule['status'] === 'holiday')
                            background-color: rgba(239, 68, 68, 0.3); /* Merah */
                            color: rgb(254, 226, 226);
                        @else
                            background-color: rgba(255, 255, 255, 0.1);
                            color: rgb(243, 244, 246);
                        @endif
                        top: 0; height: {{ (strtotime($schedule['end_time']) - strtotime($schedule['start_time'])) / 3600 * 64 }}px">
                        <div class="font-medium">
                            @if($schedule['status'] === 'available')
                                Available
                            @else
                                <div>
                                    @if($schedule['booker_name'])
                                        <div class="font-medium">{{ $schedule['booker_name'] }}</div>
                                    @endif
                                    <div class="text-xs opacity-75">
                                        @if($schedule['status'] === 'holiday')
                                            {{ $schedule['notes'] ?: 'Holiday' }}
                                        @elseif($schedule['status'] === 'booked')
                                            {{ $schedule['notes'] ?: 'Booked' }}
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="opacity-75">
                            {{ date('H:i', strtotime($schedule['start_time'])) }}-{{ date('H:i', strtotime($schedule['end_time'])) }}
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @endfor
                @php
                    $weekStart->addDay();
                @endphp
            </div>
            @endfor
        </div>
    </div>

    <!-- Tambah Jadwal Button -->
    <button onclick="openModal('addScheduleModal')" 
            class="fixed bottom-4 right-4 bg-orange-500 text-white rounded-lg px-4 py-2 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Jadwal
    </button>
</div>

<!-- Modal Tambah Jadwal -->
<div id="addScheduleModal" class="fixed inset-0 bg-black/70 hidden z-50 backdrop-blur-sm transition-all duration-300">
    <div class="min-h-screen px-4 text-center">
        <span class="inline-block h-screen align-middle">&#8203;</span>
        <div class="inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-gray-900/95 shadow-2xl rounded-2xl border border-gray-800 backdrop-blur">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-white">Tambah Jadwal</h3>
                <button onclick="closeModal('addScheduleModal')" 
                        class="text-gray-400 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.schedule.store') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Lapangan</label>
                        <select name="court_id" required 
                                class="mt-1 block w-full rounded-lg bg-gray-800/50 border-gray-700 text-white focus:border-orange-500 focus:ring focus:ring-orange-500/20 transition-all duration-200">
                            @foreach($courts as $court)
                            <option value="{{ $court->id }}">{{ $court->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tanggal</label>
                        <input type="date" name="schedule_date" required 
                                class="mt-1 block w-full rounded-lg bg-gray-800/50 border-gray-700 text-white focus:border-orange-500 focus:ring focus:ring-orange-500/20 transition-all duration-200">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Waktu Mulai</label>
                            <input type="time" name="start_time" required 
                                    class="mt-1 block w-full rounded-lg bg-gray-800/50 border-gray-700 text-white focus:border-orange-500 focus:ring focus:ring-orange-500/20 transition-all duration-200">
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Waktu Selesai</label>
                            <input type="time" name="end_time" required 
                                    class="mt-1 block w-full rounded-lg bg-gray-800/50 border-gray-700 text-white focus:border-orange-500 focus:ring focus:ring-orange-500/20 transition-all duration-200">
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Keterangan</label>
                        <input type="text" name="notes" 
                                class="mt-1 block w-full rounded-lg bg-gray-800/50 border-gray-700 text-white focus:border-orange-500 focus:ring focus:ring-orange-500/20 transition-all duration-200">
                    </div>

                    <!-- Pengulangan Jadwal -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_recurring" id="isRecurring" value="1"
                                   class="rounded bg-gray-800/50 border-gray-700 text-orange-500 
                                          focus:ring-orange-500/20 transition-all duration-200"
                                   @change="toggleRecurring()">
                            <label class="ml-2 text-sm text-gray-300">Ulangi jadwal setiap minggu</label>
                        </div>
                        
                        <!-- Pilihan Durasi Pengulangan -->
                        <div x-show="isRecurring" x-cloak
                             class="bg-gray-800/30 p-4 rounded-lg border border-gray-700">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Durasi Pengulangan</label>
                            <select name="repeat_duration" 
                                    class="w-full rounded-lg bg-gray-800/50 border-gray-700 text-white 
                                           focus:border-orange-500 focus:ring focus:ring-orange-500/20 
                                           transition-all duration-200">
                                <option value="1">1 Bulan</option>
                                <option value="2">2 Bulan</option>
                                <option value="3">3 Bulan</option>
                                <option value="6">6 Bulan</option>
                                <option value="12">12 Bulan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('addScheduleModal')"
                            class="px-4 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-800/50 transition-all duration-200">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all duration-200 shadow-lg shadow-orange-500/20">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div id="editScheduleModal" class="fixed inset-0 bg-black/70 hidden z-50 backdrop-blur-sm transition-all duration-300">
    <div class="min-h-screen px-4 text-center">
        <span class="inline-block h-screen align-middle">&#8203;</span>
        <div class="inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-gray-900/95 shadow-2xl rounded-2xl border border-gray-800 backdrop-blur">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-white">Edit Jadwal</h3>
                <button onclick="closeModal('editScheduleModal')" 
                        class="text-gray-400 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="editScheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Lapangan</label>
                        <select name="court_id" id="editCourtId" required 
                                class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white focus:border-orange-500 focus:ring-orange-500">
                            @foreach($courts as $court)
                            <option value="{{ $court->id }}">{{ $court->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Status</label>
                        <select name="status" id="editStatus" required 
                                class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white focus:border-orange-500 focus:ring-orange-500">
                            <option value="available">Available</option>
                            <option value="booked">Booked</option>
                            <option value="holiday">Holiday</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Tanggal</label>
                        <input type="date" name="schedule_date" id="editDate" required 
                               class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Waktu Mulai</label>
                            <input type="time" name="start_time" id="editStartTime" required 
                                   class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Waktu Selesai</label>
                            <input type="time" name="end_time" id="editEndTime" required 
                                   class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Keterangan</label>
                        <input type="text" name="notes" id="editNotes"
                                class="mt-1 block w-full rounded-lg bg-gray-800/50 border-gray-700 text-white focus:border-orange-500 focus:ring focus:ring-orange-500/20 transition-all duration-200">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <div class="relative" x-data="{ showDeleteOptions: false }">
                        <button type="button" 
                                @click="showDeleteOptions = !showDeleteOptions"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200">
                            Hapus Jadwal
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="showDeleteOptions" 
                             @click.away="showDeleteOptions = false"
                             class="absolute right-0 mt-2 w-56 rounded-lg bg-gray-900 shadow-lg border border-gray-700 z-50">
                            <div class="p-2">
                                <button type="button" @click="deleteRecurringSchedule(1)"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                                    Hapus 1 Bulan ke Depan
                                </button>
                                <button type="button" @click="deleteRecurringSchedule(2)"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                                    Hapus 2 Bulan ke Depan
                                </button>
                                <button type="button" @click="deleteRecurringSchedule(3)"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                                    Hapus 3 Bulan ke Depan
                                </button>
                                <button type="button" @click="deleteRecurringSchedule(6)"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                                    Hapus 6 Bulan ke Depan
                                </button>
                                <button type="button" @click="deleteRecurringSchedule(12)"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                                    Hapus 12 Bulan ke Depan
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('editScheduleModal')"
                            class="px-4 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-800">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.court-tab {
    background-color: transparent;
    border: 1px solid #4B5563;
}

.court-tab[data-selected="true"] {
    background-color: #F97316;
    border-color: #F97316;
}

/* Tambahkan animasi untuk modal */
@keyframes modalFade {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.modal-content {
    animation: modalFade 0.2s ease-out;
}

/* Styling untuk input type date dan time di Firefox */
input[type="date"]::-moz-calendar-picker-indicator,
input[type="time"]::-moz-calendar-picker-indicator {
    filter: invert(1);
}

/* Styling untuk input type date dan time di Chrome */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
}
</style>

<script>
let currentWeekOffset = {{ (int) request()->query('week', 0) }};
let selectedCourtId = {{ request()->query('court_id', $courts->first()->id) }};
let currentSchedule = null;

function changeWeek(offset) {
    currentWeekOffset += offset;
    // Reload halaman dengan parameter week offset dan court_id
    window.location.href = `{{ route('admin.schedule') }}?week=${currentWeekOffset}&court_id=${selectedCourtId}`;
}

function selectCourt(courtId) {
    selectedCourtId = courtId;
    
    // Update tampilan tab
    document.querySelectorAll('.court-tab').forEach(tab => {
        if (tab.dataset.courtId == courtId) {
            tab.dataset.selected = "true";
            tab.style.backgroundColor = "#F97316";
        } else {
            tab.dataset.selected = "false";
            tab.style.backgroundColor = "transparent";
        }
    });
    
    // Update tampilan jadwal
    document.querySelectorAll('.schedule-item').forEach(item => {
        if (item.dataset.courtId == courtId) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    // Update URL tanpa reload halaman
    const url = new URL(window.location);
    url.searchParams.set('court_id', courtId);
    window.history.pushState({}, '', url);
}

// Initialize first court as selected
document.addEventListener('DOMContentLoaded', () => {
    selectCourt(selectedCourtId);
});

// Fungsi untuk modal
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Fungsi untuk edit jadwal
function openEditModal(schedule) {
    const form = document.getElementById('editScheduleForm');
    form.action = `{{ url('/admin/schedule') }}/${schedule.id}`;
    
    currentSchedule = schedule;
    document.getElementById('editCourtId').value = schedule.court_id;
    document.getElementById('editStatus').value = schedule.status;
    document.getElementById('editDate').value = schedule.schedule_date;
    document.getElementById('editStartTime').value = schedule.start_time;
    document.getElementById('editEndTime').value = schedule.end_time;
    document.getElementById('editNotes').value = schedule.notes !== null ? schedule.notes : '';
    
    // Debug
    console.log('Schedule data:', schedule);
    console.log('Notes value:', schedule.notes);
    
    openModal('editScheduleModal');
}

function deleteRecurringSchedule(duration) {
    if (!currentSchedule) {
        console.error('No schedule selected');
        return;
    }
    
    if (confirm(`Apakah Anda yakin ingin menghapus jadwal ini dan semua pengulangan mingguan selama ${duration} bulan ke depan?`)) {
        // Gunakan URL langsung karena kita tahu strukturnya
        const url = `/admin/schedule/${currentSchedule.id}/recurring`;
        
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                duration: duration,
                _token: '{{ csrf_token() }}'
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Terjadi kesalahan pada server');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                throw new Error(data.message || 'Gagal menghapus jadwal');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus jadwal: ' + (error.message || 'Failed to fetch'));
        });
    }
}

// Event listener untuk menutup modal dengan Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
});

// Script untuk toggle recurring
document.addEventListener('alpine:init', () => {
    Alpine.data('scheduleForm', () => ({
        isRecurring: false,
        
        toggleRecurring() {
            this.isRecurring = !this.isRecurring;
            const durationSelect = document.querySelector('select[name="repeat_duration"]');
            durationSelect.disabled = !this.isRecurring;
        }
    }));
});
</script>
@endsection