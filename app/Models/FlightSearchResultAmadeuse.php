<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightSearchResultAmadeuse extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'search_input',
        'status',
        'places',
        'price_adult',
        'price_child',
        'price_baby',
        'results_html',
    ];

    protected $casts = [
        'search_input' => 'array',
    ];
}
