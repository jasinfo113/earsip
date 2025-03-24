<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Intro extends Model
{
    protected $table = 'apps_intro';

    protected $hidden = [
        'id',
        'sort',
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

    public function scopeActive(Builder $query)
    {
        $query->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getImageAttribute()
    {
        $image = $this->attributes['image'] ?? "";
        return _diskPathUrl('uploads', $image, asset('assets/images/default.png'));
    }

}
