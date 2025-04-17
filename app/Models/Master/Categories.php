<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'm_category';
    protected $fillable = [
        'image',
        'name',
        'description',
        'unit_kerja_ids',
        'penugasan_ids',
        'label',
        'sort',
        'status',
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
