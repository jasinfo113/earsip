<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Reference extends Model
{

    protected $table = 'm_references';
    
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'data',
        'status',
    ];

    public function scopeActive(Builder $query)
    {
        $query->where('status', 1);
    }

    protected $hidden = [
        'id',
        'ref',
        'description',
        'data',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
    
}
