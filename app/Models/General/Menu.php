<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Menu extends Model
{

    protected $table = 'm_menu';

    public function scopeActive(Builder $query)
    {
        $query->where('status', 1);
    }

    protected $hidden = [
        'parent',
        'has_sub',
        'sub',
        'parent_id',
        'sort',
    ];
    
}
