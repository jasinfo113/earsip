<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder as QueryBuilder;

class SiapAduan extends Model
{

    public $timestamps = false;

    protected $connection = 'siap';
    protected $table = 'tickets';

    protected $fillable = [
        'no_ticket',
        'user_nrk',
        'ticket_type_id',
        'barang',
        'deskripsi',
        'kode_pos_ticket',
        'id_wilayah',
        'no_kec',
        'foto',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'user_nrk',
        'no_ticket',
        'barang',
        'deskripsi',
        'ticket_type_id',
        'id_wilayah',
        'kode_pos_ticket',
        'no_kec',
        'foto',
        'updated_at',
        'image',
        'is_cancelable',
        'is_updateable',
        'is_rejectable',
        'is_processable',
        'status_id',
        'status_name',
        'status_description',
        'status_label',
        'next_status_ids',
        'role_ids',
        'process_role_ids',
        'user_login',
        'user_level',
        'user_pos',
        'level_actions',
    ];

    protected $appends = ['image', 'user', 'wilayah', 'pos', 'type', 'status', 'is_cancelable', 'is_updateable', 'is_rejectable', 'is_processable'];

    public function scopeApi(Builder $query, Request $request, bool $detail = false)
    {
        $user_id = $request->user()->pegawai_id;
        $user = _singleData("siap", "users", "kode_level,kode_pos", "username = '" . $user_id . "'");
        if (!$user) {
            $user = json_decode(json_encode(["kode_level" => -1, "kode_pos" => -1]));
        }
        $query->selectRaw("tickets.*");
        $query->selectRaw("tickets.no_ticket AS number,tickets.barang AS `name`,tickets.deskripsi AS description");
        $query->selectRaw("ticket_status.id AS status_id,ticket_status.name_status AS status_name,ticket_status.description AS status_description,ticket_status.label AS status_label,ticket_status.next_status_ids,ticket_status.role_ids,ticket_status.process_role_ids");
        $query->selectRaw("'" . $user_id . "' AS user_login,'" . $user->kode_level . "' AS user_level,'" . $user->kode_pos . "' AS user_pos")
            ->selectRaw("ticket_details.level_actions")
            ->join("ticket_details", "tickets.id", "ticket_details.ticket_id")
            ->joinSub(function (QueryBuilder $query) {
                $query->selectRaw("ticket_id,MAX(created_at) AS created_at")
                    ->from("ticket_details")
                    ->groupBy("ticket_id");
            }, "last_detail", function (JoinClause $join) {
                $join->on("tickets.id", "last_detail.ticket_id")
                    ->on("ticket_details.created_at", "last_detail.created_at");
            })
            ->join("ticket_status", "ticket_details.ticket_status_id", "ticket_status.id");
        $query->when($request->string('search'), function (Builder $query, string $value) {
            if ($value) {
                $query->whereAny([
                    'tickets.no_ticket',
                    'tickets.barang',
                    'tickets.deskripsi',
                ], 'LIKE', "%" . $value . "%");
            }
        })
            ->when($request->string('period'), function (Builder $query, string $value) use ($request) {
                if ($value) {
                    if ($value == 'today') {
                        $query->whereRaw('DATE(tickets.created_at) = CURDATE()');
                    } else if ($value == 'yesterday') {
                        $query->whereRaw('DATE(tickets.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)');
                    } else if ($value == 'lastweek') {
                        $query->whereRaw('DATE(tickets.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)');
                    } else if ($value == 'lastmonth') {
                        $query->whereRaw('DATE(tickets.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)');
                    } else if ($value == 'daterange' && $request->string('start_date') and $request->string('end_date')) {
                        $query->whereRaw('DATE(tickets.created_at) BETWEEN ? AND ?', [$request->string('start_date'), $request->string('end_date')]);
                    }
                }
            })
            ->when($request->string('schedule'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereRaw("LEFT(ticket_details.tgl_dianggarkan,7) = '" . $value . "'");
                }
            })
            ->when($request->string('type_ids'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('tickets.ticket_type_id', explode(',', $value));
                }
            })
            ->when($request->integer('type_id'), function (Builder $query, int $value) {
                if ($value) {
                    $query->where('tickets.ticket_type_id', $value);
                }
            })
            ->when($request->string('status_ids'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('ticket_details.ticket_status_id', explode(',', $value));
                }
            })
            ->when($request->integer('status_id'), function (Builder $query, int $value) {
                if ($value) {
                    $query->where('ticket_details.ticket_status_id', $value);
                }
            })
            ->when($request->integer('exclude'), function (Builder $query, int $value) {
                if ($value) {
                    $query->where('tickets.id', '!=', $value);
                }
            })
            ->when($request->string('sort', 'latest'), function (Builder $query, string $value) {
                if ($value == 'oldest') {
                    $query->orderByRaw('tickets.created_at ASC');
                } else {
                    $query->orderByRaw('tickets.created_at DESC');
                }
            });
        if ($detail) {
            $query->selectRaw("DATE(ticket_details.tgl_dianggarkan) AS `date`,ticket_details.tgl_dianggarkan AS scheduled_at,IF(DATEDIFF(CURDATE(),ticket_details.tgl_dianggarkan) > 0,CONCAT('Lewat ',FORMAT(DATEDIFF(CURDATE(),ticket_details.tgl_dianggarkan),0),' hari'),'') AS overdue");
        }
        if ($user->kode_level > 0) {
            if ($user && !in_array($user->kode_level, [11, 12, 13, 14, 15, 1102, 1105, 1106, 1107, 1108, 1109])) {
                if ($user->kode_level == 5) {
                    $where = "tickets.user_nrk = '" . $user_id . "'";
                } else {
                    $where = "(FIND_IN_SET('" . $user->kode_level . "', ticket_status.view_role_ids) OR FIND_IN_SET('" . $user->kode_level . "', ticket_status.process_role_ids) OR ticket_status.process_role_ids = '-1')";
                    $kode_pos = $user->kode_pos;
                    if (substr_count($kode_pos, ".") >= 2) { // pos
                        $where .= " AND tickets.kode_pos_ticket = '" . $kode_pos . "%'";
                    } else if (substr_count($kode_pos, ".") >= 1) {
                        $exp = explode(".", $kode_pos);
                        if ($exp[0] == "0" && $user->kode_level != 11) {
                            // user dinas
                            $where .= " AND tickets.id_wilayah = '0'";
                        } else if (strlen($exp[1]) == 1) {
                            // user sudin
                            $where .= " AND tickets.kode_pos_ticket LIKE '" . $kode_pos . "%'";
                        } else {
                            // user sektor
                            $where .= " AND tickets.kode_pos_ticket LIKE '" . $kode_pos . "%'";
                        }
                    }
                }
                if (isset($where)) {
                    $query->whereRaw($where);
                }
            } else {
                $where = "(FIND_IN_SET('" . $user->kode_level . "', ticket_status.view_role_ids) OR FIND_IN_SET('" . $user->kode_level . "', ticket_status.process_role_ids))";
                $query->whereRaw($where);
            }
        } else {
            $query->whereRaw("tickets.user_nrk = '" . $user_id . "'");
        }
        return $query;
    }

    public function scopePending(Builder $query, Request $request)
    {
        $user_id = $request->user()->pegawai_id;
        $user = _singleData("siap", "users", "kode_level,kode_pos", "username = '" . $user_id . "'");
        if (!$user) {
            $user = json_decode(json_encode(["kode_level" => -1, "kode_pos" => -1]));
        }
        $query->whereRaw("tickets.user_nrk != '" . $user_id . "' AND ticket_status.next_status_ids != -1 AND ticket_status.process_role_ids != -1 AND FIND_IN_SET('" . $user->kode_level . "',ticket_status.process_role_ids)");
        return $query;
    }

    public function getImageAttribute()
    {
        $image = $this->foto ?? "";
        return _diskPathUrl('siap', $image);
    }

    public function getUserAttribute()
    {
        if ($this->user_nrk) {
            return _pegawaiByNrk($this->user_nrk);
        }
        return NULL;
    }

    public function getWilayahAttribute()
    {
        $data = NULL;
        $row = DB::connection('central')->table('m_area_wilayah')->select('id_wilayah AS id', 'nama_wilayah AS name')->where('id_wilayah', $this->id_wilayah)->first();
        if ($row) {
            $data =
                [
                    'id' => (string)$row->id,
                    'name' => $row->name,
                ];
        }
        return $data;
    }

    public function getPosAttribute()
    {
        return DB::connection('siap')->table('pos')->select('kode_pos AS id', 'nama_pos AS name')->where('kode_pos', $this->kode_pos_ticket)->first();
    }

    public function getTypeAttribute()
    {
        return DB::connection('siap')->table('ticket_types')->select('id', 'name')->where('id', $this->ticket_type_id)->first();
    }

    public function getStatusAttribute()
    {
        $status =
            [
                "id" => $this->status_id,
                "name" => $this->status_name,
                "description" => $this->status_description,
                "label" => $this->status_label,
            ];
        return $status;
    }

    public function getIsCancelableAttribute()
    {
        return ($this->user_login == $this->user_nrk && $this->status_id == 1);
    }

    public function getIsUpdateableAttribute()
    {
        return ($this->user_login == $this->user_nrk && $this->status_id == 1);
    }

    public function getIsRejectableAttribute()
    {
        if (isset($this->user_login) && isset($this->status_id)) {
            $status_id = 3;
            if ($this->user_login != $this->user_nrk && ($this->process_role_ids != '-1' && in_array($this->user_level, explode(',', $this->process_role_ids))) && ($this->next_status_ids != '-1' && in_array($status_id, explode(',', $this->next_status_ids)))) {
                return true;
            }
        }
        return false;
    }

    public function getIsProcessableAttribute()
    {
        if ($this->user_login && $this->status_id) {
            if (($this->process_role_ids != '-1' && in_array($this->user_level, explode(',', $this->process_role_ids))) && $this->next_status_ids != '-1') {
                return true;
            }
            if ($this->level_actions && $this->user_level == $this->level_actions && $this->next_status_ids != '-1') {
                return true;
            }
        }
        return false;
    }

    protected function casts(): array
    {
        return [
            'user' => 'array',
            'wilayah' => 'array',
            'pos' => 'array',
            'type' => 'array',
            'status' => 'array',
            'is_cancelable' => 'boolean',
            'is_updateable' => 'boolean',
            'is_rejectable' => 'boolean',
            'is_processable' => 'boolean',
            'scheduled_at' => 'date:d F Y',
            'created_at' => 'datetime:d M Y H:i',
            'updated_at' => 'datetime:d M Y H:i',
        ];
    }
}
