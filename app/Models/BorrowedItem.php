<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class BorrowedItem extends Model
{
    use HasFactory;
    protected $table = 'borrowed_items';
    protected $fillable = [
        'user_id',
        'transaction_date',
        'description',
        'borrowed_at',
        'status',
        'returned_at',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrowedItemDetails()
    {
        return $this->hasMany(BorrowedItemDetail::class);
    }
}
