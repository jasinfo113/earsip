<?php

namespace App\Models\Api\V1;

use App\Models\General\Reference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Ticket extends Model
{
    use SoftDeletes;

    protected $table = 'ticket';

    protected $fillable = [
        'number',
        'subject',
        'message',
        'rating',
        'review',
        'duration',
        'user_id',
        'assign_id',
        'category_id',
        'priority_id',
        'status_id',
        'created_from',
        'created_by',
        'updated_from',
        'updated_by',
        'is_deleted',
        'deleted_from',
        'deleted_by',
    ];

    protected $hidden = [
        'review',
        'duration',
        'assign',
        'user_id',
        'assign_id',
        'category_id',
        'priority_id',
        'status_id',
        'created_from',
        'created_by',
        'updated_at',
        'updated_from',
        'updated_by',
        'is_deleted',
        'deleted_at',
        'deleted_from',
        'deleted_by',
        'files',
        'is_cancelable',
        'is_updatable',
        'is_resolvable',
    ];

    protected $appends = ['user', 'assign', 'category', 'priority', 'status', 'files', 'is_cancelable', 'is_updatable', 'is_responable', 'is_resolvable', 'is_completed'];

    public function scopeApi(Builder $query, $user)
    {
        $query->selectRaw("ticket.*");
        $query->whereRaw("user_id = '" . $user->nip . "'");
        return $query;
    }

    public function scopeFilter(Builder $query, Request $request)
    {
        $query->when($request->string('search'), function (Builder $query, string $value) {
            if ($value) {
                $query->whereAny([
                    'number',
                    'subject',
                    'message',
                ], 'LIKE', "%" . $value . "%");
            }
        })
            ->when($request->string('period'), function (Builder $query, string $value) use ($request) {
                if ($value) {
                    if ($value == 'today') {
                        $query->whereRaw('DATE(created_at) = CURDATE()');
                    } else if ($value == 'yesterday') {
                        $query->whereRaw('DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)');
                    } else if ($value == 'lastweek') {
                        $query->whereRaw('DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)');
                    } else if ($value == 'lastmonth') {
                        $query->whereRaw('DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)');
                    } else if ($value == 'daterange' && $request->string('start_date') and $request->string('end_date')) {
                        $query->whereRaw('DATE(created_at) BETWEEN ? AND ?', [$request->string('start_date'), $request->string('end_date')]);
                    }
                }
            })
            ->when($request->string('priority_ids'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('priority_id', explode(',', $value));
                }
            })
            ->when($request->string('category_ids'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('category_id', explode(',', $value));
                }
            })
            ->when($request->string('status_ids'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('status_id', explode(',', $value));
                }
            })
            ->when($request->string('sort', 'latest'), function (Builder $query, string $value) {
                if ($value == 'oldest') {
                    $query->oldest();
                } else {
                    $query->latest();
                }
            });
        return $query;
    }

    public function scopePending(Builder $query)
    {
        $query->whereRaw("status_id IN(1,2,3)");
        return $query;
    }

    public function getUserAttribute()
    {
        if ($this->user_id) {
            return _pegawaiByNip($this->user_id);
        }
        return NULL;
    }

    public function getAssignAttribute()
    {
        $data = DB::connection('central')->table('m_pegawai_unit_kerja_sub')->select('nama_sub_unit_kerja')->where('id_sub_unit_kerja', $this->assign_id)->first();
        $value = $data->nama_sub_unit_kerja ?? "";
        return $value;
    }

    public function getCategoryAttribute()
    {
        return DB::table('m_references')->select('id', 'name', 'label')->where('id', $this->category_id)->first();
    }

    public function getPriorityAttribute()
    {
        return DB::table('ticket_priority')->select('id', 'name', 'label')->where('id', $this->priority_id)->first();
    }

    public function getDurationAttribute()
    {
        return ($this->attributes['duration'] ? _convertTime($this->attributes['duration']) : "");
    }

    public function getStatusAttribute()
    {
        return DB::table('ticket_status')->select('name', 'label')->where('id', $this->status_id)->first();
    }

    public function getFilesAttribute()
    {
        $data = [];
        $path = config('filesystems.assets.uploads');
        $rows = _getData("default", "ticket_file", "type,`name`", "ticket_id = '" . $this->id . "' AND response_id <= 0");
        foreach ($rows as $row) {
            $data[] =
                [
                    "type" => $row->type,
                    "name" => $path . $row->name,
                ];
        }
        return $data;
    }

    public function getIsCancelableAttribute()
    {
        return ($this->status_id == 1);
    }

    public function getIsUpdatableAttribute()
    {
        return ($this->status_id == 1);
    }

    public function getIsResponableAttribute()
    {
        return (in_array($this->status_id, [2, 3]));
    }

    public function getIsResolvableAttribute()
    {
        return ($this->status_id == 3);
    }

    public function getIsCompletedAttribute()
    {
        return (in_array($this->status_id, [5, 8]));
    }

    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'review' => 'string',
            'duration' => 'string',
            'assign' => 'string',
            'category' => 'array',
            'priority' => 'array',
            'status' => 'array',
            'created_at' => 'datetime:d M Y H:i',
            'updated_at' => 'datetime:d M Y H:i',
            'deleted_at' => 'datetime:d M Y H:i',
            'is_cancelable' => 'boolean',
            'is_updatable' => 'boolean',
            'is_responable' => 'boolean',
            'is_resolvable' => 'boolean',
        ];
    }
}
