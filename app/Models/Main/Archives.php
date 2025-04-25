<?php

namespace App\Models\Main;

use App\Models\Master\Categories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Location;

class Archives extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'document';
    protected $fillable = [
        'code',
        'number',
        'ref_number',
        'date',
        'title',
        'description',
        'note',
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

    public function document_files()
    {
        return $this->hasMany(Document_file::class, 'document_id');
    }
}
