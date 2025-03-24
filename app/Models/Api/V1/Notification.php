<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    protected $table = 'fcm_notification';

    protected $hidden = [
        'user_id',
        'platform',
        'token',
        'message_id',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $appends = ['date', 'is_read'];

    public function scopeRead(Builder $query)
    {
        $query->where(['status' => 2]);
    }

    public function scopeUnRead(Builder $query)
    {
        $query->where(['status' => 1]);
    }

    public function scopePending(Builder $query)
    {
        $query->where(['status' => 0]);
    }

    public function scopeApi(Builder $query, int $user_id)
    {
        $query->where(['platform' => 'damkarone', 'ref' => 'pegawai', 'ref_id' => $user_id]);
        $query->whereIn('status', [1, 2]);
    }

    public function getImageAttribute()
    {
        $image = "";
        if ($this->attributes['image']) {
            $image = $this->attributes['image'] ?? "";
            return _diskPathUrl('input', $image, '');
        }
        return $image;
    }

    public function getDateAttribute()
    {
        return $this->created_at->format('d F Y H:i');
    }

    public function getRefDataAttribute()
    {
        return json_decode((string)$this->attributes['ref_data']);
    }

    public function getIsReadAttribute()
    {
        return ($this->status == 2);
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_read' => 'boolean',
            'created_at' => 'datetime:d M Y H:i:s',
            'updated_at' => 'datetime:d M Y H:i:s',
        ];
    }
}
