<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferFlightLeg extends Model
{
    protected $fillable = [
        'offer_flight_id',
        'direction',
        'flight_number',
        'departure_city',
        'arrival_city',
        'departure_date',
        'arrival_date',
        'departure_time',
        'arrival_time',
        'carrier_code',
        'airline_logo'
    ];

    public function offerFlight()
    {
        return $this->belongsTo(OfferFlight::class);
    }
}