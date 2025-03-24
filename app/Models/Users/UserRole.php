<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UserRole extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];
    
    protected $hidden = [
        'id',
        'status',
    ];

    public function scopeActive(Builder $query)
    {
        $query->where('status', 1);
    }

    public function privileges(){
        return $this->hasMany(UserRolePrivilege::class);
    }
}
