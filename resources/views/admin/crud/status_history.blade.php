@extends('admin.index')

@section('main')
<div class="p-4 bg-white dark:bg-dark-bg-secondary rounded-lg shadow-sm mb-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Riwayat Status Pembayaran</h2>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-300" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search & Filter -->
    <div class="mb-6">
        <div class="relative">
            <input type="text" id="searchInput" 
                   class="w-full p-3 pl-10 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                   placeholder="Cari riwayat pembayaran...">
            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-gray-500 dark:text-gray-400">Kode Booking</th>
                    <th scope="col" class="px-6 py-3 text-gray-500 dark:text-gray-400">Pengguna</th>
                    <th scope="col" class="px-6 py-3 text-gray-500 dark:text-gray-400">Metode Pembayaran</th>
                    <th scope="col" class="px-6 py-3 text-gray-500 dark:text-gray-400">Tipe</th>
                    <th scope="col" class="px-6 py-3 text-gray-500 dark:text-gray-400">Jumlah</th>
                    <th scope="col" class="px-6 py-3 text-gray-500 dark:text-gray-400">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-gray-500 dark:text-gray-400">Status</th>
                    <th scope="col" class="px-6 py-3 text-gray-500 dark:text-gray-400">Bukti</th>
                    <th scope="col" class="px-6 py-3 text-gray-500 dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($histories as $history)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700
                        @if($history->payment_type == 'down_payment')
                            bg-yellow-50 dark:bg-yellow-900/20
                        @elseif($history->payment_type == 'full_payment')
                            bg-green-50 dark:bg-green-900/20
                        @else
                            bg-white dark:bg-gray-800
                        @endif">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $history->booking->booking_code }}
                            <div class="text-xs mt-1
                                @if($history->payment_type == 'down_payment')
                                    text-yellow-600 dark:text-yellow-400
                                @elseif($history->payment_type == 'full_payment')
                                    text-green-600 dark:text-green-400
                                @endif">
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            {{ $history->booking->user->name }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            {{ $history->paymentMethod->name }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                @if($history->payment_type == 'down_payment')
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                @else
                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                @endif">
                                {{ $history->payment_type == 'down_payment' ? 'DP' : 'Lunas' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            Rp {{ number_format($history->amount_paid, 0, ',', '.') }}
                            @if($history->payment_type == 'down_payment')
                                <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                    (50% dari total)
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            {{ $history->payment_date->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full 
                                {{ $history->payment_status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                                   ($history->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 
                                   'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                                {{ ucfirst($history->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            @if($history->payment_proof)
                                <button onclick="openProofModal('{{ asset('storage/' . $history->payment_proof) }}')" 
                                        class="text-primary hover:text-primary/80 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat
                                </button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <button onclick="openEditModal('{{ $history->id }}', '{{ $history->payment_status }}')" 
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Update
                                </button>
                                <form action="{{ route('admin.status.destroy', $history) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus riwayat ini?')"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" x-data="{ loading: false }">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full transform transition-all">
            <div class="absolute top-3 right-3">
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-5">Update Status Pembayaran</h3>
                <form id="editForm" method="POST" @submit="loading = true">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status
                            </label>
                            <select name="payment_status" id="editStatus" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeModal('editModal')"
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

<!-- Proof Modal -->
<div id="proofModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeModal('proofModal')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full transform transition-all">
            <div class="absolute top-3 right-3">
                <button onclick="closeModal('proofModal')" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-5">Bukti Pembayaran</h3>
                <div class="flex justify-center">
                    <img id="proofImage" src="" alt="Bukti Pembayaran" class="max-w-full h-auto rounded-lg">
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

function openEditModal(id, status) {
    const form = document.getElementById('editForm');
    form.action = `/admin/status/${id}`;
    document.getElementById('editStatus').value = status;
    openModal('editModal');
}

function openProofModal(imageUrl) {
    document.getElementById('proofImage').src = imageUrl;
    openModal('proofModal');
}

// Fungsi pencarian
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>
@endsection
