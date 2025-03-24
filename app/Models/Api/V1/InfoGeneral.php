<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Model;

class InfoGeneral extends Model
{
    protected $table = 'apps_general';

    protected $hidden = [
        'id',
        'updated_at',
        'updated_from',
        'updated_by',
    ];

    public function getLogoAttribute()
    {
        $image = $this->attributes['logo'] ?? "";
        return _diskPathUrl('uploads', $image, asset('assets/images/default.png'));
    }

}
