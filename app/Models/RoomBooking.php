<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class RoomBooking extends Model
{
    use HasFactory;
    protected $table = 'room_bookings';
    protected $fillable = [
        'user_id',
        'transaction_date',
        'description',
        'borrowed_at',
        'status',
        'returned_at',
        'booking_category',
        'total_price',
    ];

    protected $attributes = [
        'status' => 'Pending',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (is_null($model->transaction_date)) {
                $model->transaction_date = Carbon::now();
            }
        });
    }

    public function calculatePrice()
    {
        $totalHarga = 0;
        $tanggalPinjam = $this->borrowed_at;
        $tanggalKembali = $this->returned_at;

        $jumlahHari = ($tanggalPinjam && $tanggalKembali) ? \Carbon\Carbon::parse($tanggalPinjam)->diffInDays(\Carbon\Carbon::parse($tanggalKembali)) + 1 : 1;

        foreach ($this->roomBookingDetails as $detail) {
            $totalHarga += $detail->room->price * $jumlahHari;
        }

        return $totalHarga;

    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roomBookingDetails()
    {
        return $this->hasMany(RoomBookingDetail::class);
    }

    // RoomBooking.php
    public function roomBookingExternal()
    {
        return $this->hasOne(RoomBookingExternal::class, 'room_booking_id', 'id');
    }

}
