<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    protected $fillable = ['iata_code', 'name', 'domain'];

    public function getLogoUrlAttribute()
    {
        if (!$this->domain) {
            return asset('images/default-airline.png');
        }
        
        return 'https://logo.clearbit.com/'.$this->domain.'?size=150';
    }
}