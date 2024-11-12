<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class RoomBookingDetail extends Model
{
    use HasFactory;

    protected $table = 'room_booking_details';
    protected $fillable = [
        'room_booking_id',
        'room_id',
        'status',
        'borrowed_condition',
        'returned_condition',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $room = $model->room;
            if ($room->status === 'Available') {
                $room->update(['status' => 'Booked']);
            }

        });

        static::saved(function ($model) {
            $roomBooking = $model->roomBooking;
            if ($roomBooking->booking_category === 'external') {
                $totalHarga = $roomBooking->calculatePrice();
                $roomBooking->update(['total_price' => $totalHarga]);
            }else{
                $roomBooking->update(['total_price' => 'FREE']);
            }
        });
    }

    public function roomBooking()
    {
        return $this->belongsTo(RoomBooking::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
