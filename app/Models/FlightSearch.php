<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightSearch extends Model
{
    protected $fillable = [
        'user_id',
        'search_params',
        'results',
        'status',
        'error_message',
        'places',
    'price_adult',
    'price_child',
    'price_baby',
    'type',
  
    ];

    protected $casts = [
        'search_params' => 'array',
        'results' => 'array',
    ];
}
