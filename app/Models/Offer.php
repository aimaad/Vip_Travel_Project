<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    protected $table = 'flight_offers';
    
    protected $fillable = [
        'type',
        'seats_available',
        'status',
        'author_id',
        'flight_data'
    ];

    protected $casts = [
        'flight_data' => 'array'
    ];
    
    public function flights(): HasMany
    {
        return $this->hasMany(OfferFlight::class);
    }
    
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}