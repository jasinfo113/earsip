<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Clients extends Model
{
    use Notifiable, HasApiTokens, SoftDeletes,HasFactory;
    protected $table = 'clients';

    protected $fillable = [
        'image',
        'penugasan_ids',
        'name',
        'client_id',
        'client_secret',
        'url_web',
        'url_auth',
        'api',
        'web',
        'status',
        'created_at',
        'created_from',
        'created_by',
        'updated_at',
        'updated_from',
        'updated_by',
        'is_deleted',
        'deleted_at',
        'deleted_from',
        'deleted_by',
    ];
}
