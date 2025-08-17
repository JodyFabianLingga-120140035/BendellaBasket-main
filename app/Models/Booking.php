<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_code',
        'user_id',
        'court_id',
        'schedule_id',
        'duration',
        'total_price',
        'down_payment',
        'notes',
        'status',
        'terms_accepted',
        'terms_accepted_at',
        'is_member_booking',
        'recurring_day',
        'recurring_start_time',
        'recurring_end_time',
        'recurring_until'
    ];

    // Tambahkan konstanta untuk status yang valid
    const STATUS_PENDING = 'pending';
    const STATUS_WAITING_PAYMENT = 'waiting_payment';
    const STATUS_PAID = 'paid';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $casts = [
        'terms_accepted' => 'boolean',
        'terms_accepted_at' => 'datetime',
        'is_member_booking' => 'boolean',
        'recurring_until' => 'date',
        'recurring_start_time' => 'datetime',
        'recurring_end_time' => 'datetime',
        'status' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function paymentHistory()
    {
        return $this->hasMany(PaymentStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function court()
    {
        return $this->belongsTo(BasketCourt::class, 'court_id');
    }

    // Tambahkan validasi status
    public static function getValidStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_WAITING_PAYMENT,
            self::STATUS_PAID,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED
        ];
    }
}
