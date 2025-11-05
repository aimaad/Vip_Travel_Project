<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';
    protected $guarded = [];
    
    public function offre()
    {
        return $this->belongsTo(Offre::class);
    }
}