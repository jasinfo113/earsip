<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRolePrivilege extends Model
{
    use SoftDeletes;
    
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'menu_id',
        'read',
        'create',
        'update',
        'delete',
        'export',
        'approve',
        'created_at',
        'created_from',
        'created_by',
        'updated_at',
        'updated_from',
        'updated_by',
    ];

    protected $hidden = [
        'role_id',
        'menu_id',
        'read',
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

    protected $appends = ['menu'];

    public function scopeRead(Builder $query)
    {
        $query->where('read', 1);
    }

    public function getMenuAttribute()
    {
        return Menu::active()->find($this->menu_id);
    }
}
