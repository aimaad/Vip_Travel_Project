<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;

class Offre extends Model
{
    protected $fillable = [
        'id',
        'total_rooms',
        'room_types',
    ];

    protected $casts = [
        'room_types' => 'array', 
    ];

    public function hotel_scraping()
    {
        return $this->belongsTo(\App\Models\HotelScraping::class, 'hotel_scraping_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Accessor, PAS une relation Eloquent !
    public function getServicesAttribute()
    {
        if (!$this->service_ids) {
            return collect();
        }
        $serviceIds = json_decode($this->service_ids, true) ?? [];
        if (!is_array($serviceIds) || empty($serviceIds)) {
            return collect();
        }
        return Service::whereIn('id', $serviceIds)->get();
    }
    public function offerFlights()
{
    return $this->hasMany(OfferFlight::class);
}
public function flight(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(\App\Models\OfferFlight::class, 'offre_id');
}
}