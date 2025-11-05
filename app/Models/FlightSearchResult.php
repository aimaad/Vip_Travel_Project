<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightSearchResult extends Model
{
    protected $table = 'flight_search_results';
    protected $guarded = [];
    protected $casts = [
        'search_input' => 'array',
        'results'      => 'array',
    ];
}