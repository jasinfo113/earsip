<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EsatgasRedkar extends Model
{

    public $timestamps = false;

    protected $connection = 'esatgas';
    protected $table = 'redkar';
    protected $primaryKey = 'id_redkar';

    protected $fillable = [
        'id_redkar',
        'no_pegawai',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'id_rw',
        'no_rw',
        'no_rt',
        'nik',
        'nm_redkar',
        'tmpt_lahir',
        'tgl_lahir',
        'no_telepon',
        'keterangan',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'id_redkar',
        'no_pegawai',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'id_rw',
        'no_rw',
        'no_rt',
        'keterangan',
        'created_at',
        'updated_at',
        'is_deletable',
        'is_updatable',
        'user_login',
        'user_kelurahan',
    ];

    protected $appends =
    [
        'location',
        'user',
        'is_deletable',
        'is_updatable',
    ];

    public function scopeApi(Builder $query, $user)
    {
        $query->selectRaw("redkar.id_redkar AS id,redkar.keterangan AS note");
        $query->selectRaw("redkar.*");
        $query->selectRaw("'" . $user->nrk . "' AS user_login, '" . $user->kelurahan->id . "' AS user_kelurahan");
        if (!in_array($user->role_id, [1, 2, 3]) && !$user->bypass_area) {
            if (in_array($user->level->id, [99])) {
                $query->whereRaw("(no_kelurahan = '" . $user->kelurahan->id . "')");
            } else {
                $pos_id = $user->pos->id;
                $where_area = NULL;
                if (substr_count($pos_id, ".") >= 2) {
                    // user pos
                    $where_area = "no_kelurahan IN(" . implode(",", array_map("intval", $user->villages)) . ")";
                } else if (substr_count($pos_id, ".") >= 1) {
                    $exp = explode(".", $pos_id);
                    if ($exp[0] == "0" && $user->kode_level != 11) {
                        // user dinas
                    } else if (strlen($exp[1]) == 1) {
                        // user sudin
                        $where_area = "no_kota IN(" . implode(",", array_map("intval", $user->cities)) . ")";
                    } else {
                        // user sektor
                        $where_area = "no_kecamatan IN(" . implode(",", array_map("intval", $user->districts)) . ")";
                    }
                }
                if (isset($where_area)) {
                    $query->whereRaw($where_area);
                }
            }
        }
        return $query;
    }

    public function scopeFilter(Builder $query, Request $request)
    {
        $query->when($request->string('search'), function (Builder $query, string $value) {
            if ($value) {
                $query->whereAny([
                    'redkar.nm_redkar',
                    'redkar.no_telepon',
                    'redkar.keterangan',
                ], 'LIKE', "%" . $value . "%");
            }
        })
            ->when($request->string('period'), function (Builder $query, string $value) use ($request) {
                if ($value) {
                    if ($value == 'today') {
                        $query->whereRaw('DATE(redkar.created_at) = CURDATE()');
                    } else if ($value == 'yesterday') {
                        $query->whereRaw('DATE(redkar.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)');
                    } else if ($value == 'lastweek') {
                        $query->whereRaw('DATE(redkar.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)');
                    } else if ($value == 'lastmonth') {
                        $query->whereRaw('DATE(redkar.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)');
                    } else if ($value == 'daterange' && $request->string('start_date') and $request->string('end_date')) {
                        $query->whereRaw('DATE(redkar.created_at) BETWEEN ? AND ?', [$request->string('start_date'), $request->string('end_date')]);
                    }
                }
            })
            ->when($request->string('sort', 'name_asc'), function (Builder $query, string $value) {
                if ($value == 'name_desc') {
                    $query->orderByRaw('redkar.nm_redkar DESC');
                } else {
                    $query->orderByRaw('redkar.nm_redkar ASC');
                }
            });
        return $query;
    }

    public function getLocationAttribute()
    {
        $address = $this->nama_kelurahan . ", " . $this->nama_kecamatan . " - " . $this->nama_kota;
        $address .= "\nRT: " . $this->no_rt . " RW: " . $this->no_rw;
        $data =
            [
                "alamat" => (string)$address,
            ];
        return $data;
    }

    public function getStatusAttribute()
    {
        $id = $this->status_redkar;
        $name = "";
        $label = "";
        if ($id == "DISETUJUI") {
            $id = 5;
            $name = "Disetujui";
            $label = "success";
        } else if ($id == "DITOLAK") {
            $id = 9;
            $name = "Ditolak";
            $label = "danger";
        } else {
            $id = 0;
            $name = "Belum Diverifikasi";
            $label = "warning";
        }
        $data =
            [
                "id" => (int)$id,
                "name" => (string)$name,
                "label" => (string)$label,
            ];
        return $data;
    }

    public function getUserAttribute()
    {
        if ($this->no_pegawai) {
            return _pegawaiByNrk($this->no_pegawai);
        }
        return NULL;
    }

    public function getIsDeletableAttribute()
    {
        return (($this->user_kelurahan == $this->no_kelurahan));
    }

    public function getIsUpdatableAttribute()
    {
        return (($this->user_kelurahan == $this->no_kelurahan));
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'nik' => 'integer',
            'no_telepon' => 'string',
            'location' => 'array',
            'user' => 'array',
            'tgl_lahir' => 'date:d F Y',
            'created_at' => 'datetime:d M Y H:i',
            'updated_at' => 'datetime:d M Y H:i',
            'is_deletable' => 'boolean',
            'is_updatable' => 'boolean',
        ];
    }
}
