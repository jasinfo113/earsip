<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archives extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'document';
    protected $fillable = [
        'code',
        'number',
        'date',
        'title',
        'description',
        'type',
        'unit_kerja_id',
        'penugasan_id',
        'category_id',
        'tag_ids',
        'location_id',
        'status_id',
        'viewed',
        'printed',
        'downloaded',
        'created_at',
        'created_from',
        'created_by',
        'updated_at',
        'updated_from',
        'updated_by'
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
