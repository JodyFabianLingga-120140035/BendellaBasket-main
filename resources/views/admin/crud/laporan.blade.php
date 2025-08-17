@extends('admin.index')

@section('main')
<div class="p-4 bg-gray-900 text-white min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-orange-500 to-orange-300 bg-clip-text text-transparent">
                Laporan Booking
            </h2>
        </div>

        <!-- Filter Section -->
        <div class="bg-gray-800/50 rounded-xl p-6 mb-6 border border-gray-700/50">
            <form action="{{ route('admin.laporan') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Filter Lapangan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Lapangan</label>
                        <select name="court_id" class="w-full rounded-lg bg-gray-800 border-gray-700 text-white">
                            <option value="">Semua Lapangan</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>
                                    {{ $court->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Bulan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Bulan</label>
                        <select name="month" class="w-full rounded-lg bg-gray-800 border-gray-700 text-white">
                            <option value="">Semua Bulan</option>
                            @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Tahun -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tahun</label>
                        <select name="year" class="w-full rounded-lg bg-gray-800 border-gray-700 text-white">
                            @foreach(range(date('Y')-1, date('Y')+1) as $year)
                                <option value="{{ $year }}" {{ request('year', date('Y')) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select name="status" class="w-full rounded-lg bg-gray-800 border-gray-700 text-white">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all duration-200">
                        Filter
                    </button>
                    <a href="{{ route('admin.laporan.export', request()->all()) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200">
                        Download PDF
                    </a>
                </div>
            </form>
        </div>

        <!-- Laporan Table -->
        <div class="bg-gray-800/50 rounded-xl border border-gray-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-700/50 text-gray-300">
                        <tr>
                            <th class="px-6 py-4">Kode Booking</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Jam</th>
                            <th class="px-6 py-4">Lapangan</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Durasi</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Metode Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-700/30">
                                <td class="px-6 py-4">{{ $booking->booking_code }}</td>
                                <td class="px-6 py-4">
                                    @if($booking->schedule && $booking->schedule->schedule_date)
                                        {{ $booking->schedule->schedule_date->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($booking->schedule && $booking->schedule->start_time && $booking->schedule->end_time)
                                        {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($booking->schedule->end_time)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ optional($booking->court)->name }}</td>
                                <td class="px-6 py-4">{{ optional($booking->user)->name }}</td>
                                <td class="px-6 py-4">{{ $booking->duration ?? '0' }} Jam</td>
                                <td class="px-6 py-4">Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status == 'paid') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $latestPayment = $booking->paymentHistory->first();
                                        $paymentMethod = $latestPayment ? $latestPayment->paymentMethod : null;
                                    @endphp
                                    {{ $paymentMethod ? $paymentMethod->name : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-400">
                                    Tidak ada data booking
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-800/30">
                {{ $bookings->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection