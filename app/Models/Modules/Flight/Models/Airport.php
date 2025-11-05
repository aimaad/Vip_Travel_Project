<?php

namespace App\Models\Modules\Flight\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\Sluggable;
use Modules\Flight\Factories\AirportFactory;
use GeoIp2\Model\City;
use GeoIp2\Model\Country;
use Modules\Flight\Models\Flight;



class Airport extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'airports';
    protected $fillable = [
        'name',
        'code',
        'location',
        'description',
        'address',
        'country_code',
        'city_code',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    protected static function newFactory()
    {
        return AirportFactory::new();
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    // Relations
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function departureFlights()
    {
        return $this->hasMany(Flight::class, 'departure_airport_id');
    }

    public function arrivalFlights()
    {
        return $this->hasMany(Flight::class, 'arrival_airport_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', strtoupper($code));
    }

    // Helpers
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    public function getLocationNameAttribute()
    {
        return $this->city->name . ', ' . $this->country->name;
    }
}