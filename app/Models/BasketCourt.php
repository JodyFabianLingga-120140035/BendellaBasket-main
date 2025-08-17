<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BasketCourt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'description',
        'price_per_hour',
        'is_available',
        'photo',
        'status',
        'holiday_dates',
        'holiday_start_time',
        'holiday_end_time'
    ];

    protected $casts = [
        'holiday_dates' => 'array',
        'is_available' => 'boolean',
        'holiday_start_time' => 'datetime',
        'holiday_end_time' => 'datetime'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'court_id');
    }

    public function updateScheduleStatus($status)
    {
        // Update jadwal hanya untuk tanggal dan waktu yang sesuai dengan holiday_dates
        if ($status === 'inactive' && $this->holiday_dates) {
            foreach ($this->holiday_dates as $date) {
                $this->schedules()
                    ->where('schedule_date', $date)
                    ->where('start_time', '>=', $this->holiday_start_time)
                    ->where('end_time', '<=', $this->holiday_end_time)
                    ->where('status', '!=', 'booked')
                    ->update(['status' => 'holiday']);
            }
        } else if ($status === 'active') {
            // Kembalikan status jadwal yang holiday ke available
            $this->schedules()
                ->where('status', 'holiday')
                ->update(['status' => 'available']);
        }
    }

    public function isHoliday()
    {
        if (!$this->holiday_dates) return false;
        
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');
        
        if (in_array($today, $this->holiday_dates) && 
            $currentTime >= $this->holiday_start_time->format('H:i:s') && 
            $currentTime <= $this->holiday_end_time->format('H:i:s')) {
            $this->update(['status' => 'inactive']);
            $this->updateScheduleStatus('inactive');
            return true;
        }
        
        if ($this->status === 'inactive') {
            $this->update(['status' => 'active']);
            $this->updateScheduleStatus('active');
        }
        
        return false;
    }

    public static function checkAndUpdateHolidayStatus()
    {
        $courts = self::all();
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');
        
        foreach ($courts as $court) {
            if ($court->holiday_dates && 
                in_array($today, $court->holiday_dates) && 
                $currentTime >= $court->holiday_start_time->format('H:i:s') && 
                $currentTime <= $court->holiday_end_time->format('H:i:s')) {
                $court->update(['status' => 'inactive']);
                $court->updateScheduleStatus('inactive');
            } else {
                // Hanya ubah status ke active jika saat ini bukan jadwal libur
                if (!$court->isHoliday()) {
                    $court->update(['status' => 'active']);
                    $court->updateScheduleStatus('active');
                }
            }
        }
    }
}