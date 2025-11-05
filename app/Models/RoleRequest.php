<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


class RoleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'agency_name',
        'other_agency_name',
        'iata_office_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
        static::creating(function ($roleRequest) {
            Log::info('Création d\'une nouvelle demande de rôle pour l\'utilisateur ID: ' . $roleRequest->user_id . ', Rôle: ' . $roleRequest->type);
        });

        static::created(function ($roleRequest) {
            Log::info('Nouvelle demande de rôle sauvegardée avec succès, ID de la demande: ' . $roleRequest->id);
        });
    }
}
