<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SiapAduanDetail extends Model
{

    public $timestamps = false;

    protected $connection = 'siap';
    protected $table = 'ticket_details';

    protected $fillable = [
        'nrk',
        'ticket_id',
        'ticket_status_id',
        'foto',
        'deskripsi',
        'tgl_dianggarkan',
        'level_actions',
        'cron',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'nrk',
        'ticket_id',
        'deskripsi',
        'ticket_status_id',
        'tgl_dianggarkan',
        'foto',
        'level_actions',
        'cron',
        'updated_at',
    ];

    protected $appends = ['image', 'user', 'status', 'is_completed', 'is_closed'];

    public function scopeApi(Builder $query, int $ticket_id)
    {
        $query->selectRaw("*");
        $query->selectRaw("deskripsi AS description,tgl_dianggarkan AS scheduled_at,IF(DATEDIFF(CURDATE(),tgl_dianggarkan) > 0,CONCAT('Lewat ',FORMAT(DATEDIFF(CURDATE(),tgl_dianggarkan),0),' hari'),'') AS overdue");
        $query->where("ticket_id", $ticket_id);
        return $query;
    }

    public function getImageAttribute()
    {
        $image = $this->foto ?? "";
        return _diskPathUrl('siap', $image);
    }

    public function getUserAttribute()
    {
        if ($this->nrk) {
            return _pegawaiByNrk($this->nrk);
        }
        return NULL;
    }

    public function getStatusAttribute()
    {
        $status = DB::connection('siap')->table('ticket_status')->select('id', 'name_status AS name', 'description', 'label')->where('id', $this->ticket_status_id)->first();
        if (!$status) {
            $status =
                [
                    "id" => -1,
                    "name" => "Unknown",
                    "description" => "Unknown status",
                    "label" => "default",
                ];
        }
        return $status;
    }

    public function getIsCompletedAttribute()
    {
        $status_id = $this->getStatusAttribute()->id ?? -1;
        return ($status_id == 10);
    }

    public function getIsClosedAttribute()
    {
        $status_id = $this->getStatusAttribute()->id ?? -1;
        return in_array($status_id, [3, 88]);
    }

    protected function casts(): array
    {
        return [
            'user' => 'array',
            'status' => 'array',
            'is_completed' => 'boolean',
            'is_closed' => 'boolean',
            'scheduled_at' => 'date:d F Y',
            'created_at' => 'datetime:d M Y H:i',
            'updated_at' => 'datetime:d M Y H:i',
        ];
    }
}
