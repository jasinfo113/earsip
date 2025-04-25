<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Document_log extends Model
{
    public $timestamps = false;
    protected $table = 'document_log';
    protected $fillable = [
        'document_id',
        'file_id',
        'description',
        'print',
        'download',
        'ip_address',
        'user_agent',
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
