<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TicketResponse extends Model
{

    protected $table = 'ticket_response';

    protected $fillable = [
        'id',
        'ticket_id',
        'title',
        'message',
        'notify',
        'ip_address',
        'user_agent',
        'created_at',
        'created_from',
        'created_by',
    ];

    protected $hidden = [
        'ticket_id',
        'notify',
        'created_from',
        'created_by',
    ];

    protected $appends = ['user', 'files'];

    public function scopeApi(Builder $query, int $ticket_id)
    {
        $query->selectRaw("ticket_response.*");
        $query->whereRaw("ticket_id = '" . $ticket_id . "'");
        return $query;
    }

    public function scopeFilter(Builder $query, Request $request)
    {
        $query->when($request->string('search'), function (Builder $query, string $value) {
            if ($value) {
                $query->whereAny([
                    'title',
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
            ->when($request->string('sort', 'latest'), function (Builder $query, string $value) {
                if ($value == 'oldest') {
                    $query->oldest();
                } else {
                    $query->latest();
                }
            });
        return $query;
    }

    public function getUserAttribute()
    {
        if ($this->created_from && $this->created_by > 0) {
            if ($this->created_from == "Apps") {
                return _pegawaiByNip($this->created_by);
            } else if ($this->created_from == "Back Office") {
                $row = _singleData("default", "users", "photo,`name`", "id = '" . $this->created_by . "'");
                if ($row) {
                    $photo = $row->photo;
                    $photo = _diskPathUrl('uploads', $photo, asset('assets/images/nophoto.png'));
                    $data =
                        [
                            'photo' => $photo,
                            'nama' => $row->name,
                        ];
                    return $data;
                }
            }
        }
        $photo = asset('assets/images/default.png');
        $data =
            [
                'photo' => $photo,
                'nama' => "System",
            ];
        return $data;
    }

    public function getFilesAttribute()
    {
        $data = [];
        $path = config('filesystems.assets.uploads');
        $rows = _getData("default", "ticket_file", "type,`name`", "ticket_id = '" . $this->ticket_id . "' AND response_id = '" . $this->id . "'");
        foreach ($rows as $row) {
            $data[] =
                [
                    "type" => $row->type,
                    "name" => $path . $row->name,
                ];
        }
        return $data;
    }

    protected function casts(): array
    {
        return [
            'notify' => 'boolean',
            'created_at' => 'datetime:d M Y H:i',
        ];
    }
}
