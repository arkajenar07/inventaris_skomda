<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    // Tentukan nama tabel, jika tidak sesuai dengan nama model dalam bentuk jamak
    protected $table = 'buildings';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'name',
        'code',
    ];

    // Tentukan tipe data kolom yang perlu di-cast
    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
