<?php

namespace App\Models\Api;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
 
class AccessToken extends SanctumPersonalAccessToken
{

    protected $table = 'access_tokens';

    protected $appends = ['is_valid'];

    protected $hidden = [
        'id',
        'abilities',
        'tokenable',
        'tokenable_type',
        'tokenable_id',
        'name',
        'last_used_at',
        'created_at',
        'updated_at',
    ];

    public function getIsValidAttribute()
    {
        return (bool)($this->expires_at > now());
    }

}