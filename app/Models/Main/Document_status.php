<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Document_status extends Model
{
    protected $table = 'document_status';
    protected $fillable = [
        'name',
        'label',
        'access',

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
