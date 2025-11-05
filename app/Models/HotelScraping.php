<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelScraping extends Model
{
    protected $fillable = ['hotel_name', 'images', 'address', 'rating'];

    protected $casts = [
        'images' => 'array', // ✅ Laravel convertira automatiquement en tableau
    ];
}
