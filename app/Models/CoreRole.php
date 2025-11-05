<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreRole extends Model
{
    use HasFactory;

    protected $table = 'core_roles';

    protected $fillable = [
        'name', 
        'code', 
        'commission', 
        'commission_type', 
        'create_user', 
        'update_user', 
        'status'
    ];
}
