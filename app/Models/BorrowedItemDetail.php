<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BorrowedItemDetail extends Model
{
    use HasFactory;
    protected $table = 'borrowed_item_details';
    protected $fillable = [
        'borrowed_item_id',
        'item_id',
        'status',
        'borrowed_condition',
        'returned_condition',
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $item = $model->item;
            if ($item->status === 'Available') {
                $item->update(['status' => 'Borrowed']);
            }
        });
    }
    public function borrowedItem()
    {
        return $this->belongsTo(BorrowedItem::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
