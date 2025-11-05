<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    protected $fillable = [
        'iata_code',
        'name',
        'domain',
        'logo_url'
    ];

    // Accessor pour le logo
    public function getLogoAttribute()
    {
        return $this->logo_url ?? 'https://logo.clearbit.com/'.$this->domain.'?size=150';
    }
}