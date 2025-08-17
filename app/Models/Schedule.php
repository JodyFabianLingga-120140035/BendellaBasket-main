<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'court_id',
        'schedule_date',
        'start_time',
        'end_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    public function court()
    {
        return $this->belongsTo(BasketCourt::class, 'court_id');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'schedule_id');
    }

    // Tambahkan scope untuk jadwal yang belum di-booking
    public function scopeNotBooked($query)
    {
        return $query->where('status', '!=', 'booked');
    }

    // Tambahkan scope untuk jadwal yang aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'available');
    }

    // Tambahkan scope untuk jadwal yang libur
    public function scopeHoliday($query)
    {
        return $query->where('status', 'holiday');
    }
}
