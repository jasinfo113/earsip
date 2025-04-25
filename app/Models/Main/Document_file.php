<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Document_file extends Model
{
    public $timestamps = false;
    protected $table = 'document_file';
    protected $fillable = [
        'document_id',
        'name',
        'description',
        'sort',
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
