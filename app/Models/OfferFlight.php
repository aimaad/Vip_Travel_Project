<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OfferFlight extends Model
{
    protected $fillable = [
        'offre_id', 'places', 'price_adult', 'price_child', 'price_baby', 'flight_type'
    ];

    public function offre()
    {
        return $this->belongsTo(Offre::class);
    }

    public function flightLegs()
    {
        return $this->hasMany(OfferFlightLeg::class);
    }
}