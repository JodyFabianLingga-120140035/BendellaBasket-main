<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'account_number',
        'account_name',
        'qris_img',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'integer',
    ];

    public function paymentHistory()
    {
        return $this->hasMany(PaymentStatusHistory::class);
    }
}
