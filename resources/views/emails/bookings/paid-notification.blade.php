@component('mail::message')
# Booking Baru Telah Dibayar

Booking dengan kode **{{ $booking->booking_code }}** telah dibayar.

**Detail Booking:**
- Nama Customer: {{ $booking->user->name }}
- Lapangan: {{ $booking->court->name }}
- Tanggal: {{ $booking->schedule->schedule_date->format('d M Y') }}
- Waktu: {{ $booking->schedule->start_time->format('H:i') }} - {{ $booking->schedule->end_time->format('H:i') }} WIB
- Total Pembayaran: Rp {{ number_format($booking->total_price, 0, ',', '.') }}

@component('mail::button', ['url' => route('admin.booking')])
Lihat Detail Booking
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent 