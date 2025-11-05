<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'type',
        'adults',
        'children',
        'kids',
        'babies',
        'price',
        'available_rooms',
        'pension'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
