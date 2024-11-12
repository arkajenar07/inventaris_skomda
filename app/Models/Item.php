<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    // Nama tabel jika berbeda dengan nama model dalam bentuk jamak
    protected $table = 'items';

    // Kolom yang bisa diisi
    protected $fillable = [
        'created_by',
        'code',
        'name',
        'category_id',
        'description',
        'spesification',
        'origin_of_acquisition',
        'building_id',
        'room_id',
        'series_number',
        'brand',
        'type',
        'color',
        'quantity',
        'procurement_year',
        'price',
        'registration_date',
        'photo',
        'status',
        'barcode',
        'note',
    ];

    // Tipe data yang perlu di-cast
    protected $casts = [
        'quantity' => 'integer',
        'procurement_year' => 'integer',
        'price' => 'decimal:2',
        'registration_date' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Definisi relasi ke model User (created_by)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Definisi relasi ke model Category (category_id)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Definisi relasi ke model Building (building_id)
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    // Definisi relasi ke model Room (room_id)
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
