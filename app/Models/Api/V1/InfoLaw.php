<?php

namespace App\Models\Api\V1;

use App\Models\General\Reference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class InfoLaw extends Model
{
    protected $table = 'cms_info_law';

    protected $hidden = [
        'id',
        'ref_id',
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

    protected $appends = ['ref'];

    public function scopeActive(Builder $query)
    {
        $query->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getFileAttribute()
    {
        $file = $this->attributes['file'] ?? "";
        return _diskPathUrl('uploads', $file, '');
    }

    public function getRefAttribute()
    {
        return Reference::active()->find($this->ref_id);
    }

    protected function casts(): array
    {
        return [
            'date' => 'date:d F Y',
        ];
    }
}
