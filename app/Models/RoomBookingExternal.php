<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RoomBookingExternal extends Model
{
    use HasFactory;

    // RoomBookingExternal.php
    protected $table = 'room_booking_externals';
    protected $fillable = [
        'room_booking_id',
        'customer_name',
        'customer_photo',
        'customer_phone',
        'customer_company',
        'payment_status',
    ];

    protected $attributes = [
        'payment_status' => 'Pending',
    ];

    public function roomBooking()
    {
        return $this->belongsTo(RoomBooking::class);
    }
}
