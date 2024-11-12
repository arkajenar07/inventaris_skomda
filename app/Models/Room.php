<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    // Nama tabel jika berbeda dengan nama model dalam bentuk jamak
    protected $table = 'rooms';

    // Kolom yang bisa diisi
    protected $fillable = [
        'building_id',
        'name',
        'code',
        'price',
        'status',
        'description',
    ];

    // Tipe data yang perlu di-cast
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // Definisi relasi ke model Building
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
