<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .filters {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Booking</h2>
        <p>Tanggal: {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="filters">
        <p><strong>Filter yang digunakan:</strong></p>
        @if(!empty($filters['court_id']))
            <p>Lapangan: {{ \App\Models\BasketCourt::find($filters['court_id'])->name ?? '-' }}</p>
        @endif
        @if(!empty($filters['month']))
            <p>Bulan: {{ \Carbon\Carbon::create()->month($filters['month'])->format('F') }}</p>
        @endif
        @if(!empty($filters['year']))
            <p>Tahun: {{ $filters['year'] }}</p>
        @endif
        @if(!empty($filters['status']))
            <p>Status: {{ ucfirst($filters['status']) }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Booking</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Lapangan</th>
                <th>Customer</th>
                <th>Durasi</th>
                <th>Total</th>
                <th>Status</th>
                <th>Metode Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_code }}</td>
                    <td>
                        @if($booking->schedule && $booking->schedule->schedule_date)
                            {{ $booking->schedule->schedule_date->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($booking->schedule && $booking->schedule->start_time && $booking->schedule->end_time)
                            {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($booking->schedule->end_time)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ optional($booking->court)->name }}</td>
                    <td>{{ optional($booking->user)->name }}</td>
                    <td>{{ $booking->duration ?? '0' }} Jam</td>
                    <td>Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($booking->status) }}</td>
                    <td>
                        @php
                            $latestPayment = $booking->paymentHistory->first();
                            $paymentMethod = $latestPayment ? $latestPayment->paymentMethod : null;
                        @endphp
                        {{ $paymentMethod ? $paymentMethod->name : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data booking</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html> 