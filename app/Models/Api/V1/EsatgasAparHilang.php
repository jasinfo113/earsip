<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EsatgasAparHilang extends Model
{

    public $timestamps = false;

    protected $connection = 'esatgas';
    protected $table = 'sarop_kehilangan_apar_new';
    protected $primaryKey = 'id_apar';

    protected $fillable = [
        'id_apar',
        'nik',
        'tmpt_lahir',
        'tgl_lahir',
        'nm_penanggung_jawab',
        'no_telepon',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'no_rt',
        'idrw',
        'no_rw',
        'alamat',
        'jenis_apar',
        'tahun',
        'tautan_foto',
        'keterangan',
        'no_pegawai',
        'created_at',
        'updated_at',
        'updated_by',
    ];

    protected $hidden = [
        'id_apar',
        'no_apar',
        'tautan_foto',
        'nik',
        'tmpt_lahir',
        'tgl_lahir',
        'nm_penanggung_jawab',
        'no_telepon',
        'keterangan',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'no_rt',
        'idrw',
        'no_rw',
        'alamat',
        'no_pegawai',
        'created_by',
        'updated_at',
        'updated_by',
        'is_deletable',
        'is_updatable',
        'user_login',
        'user_kelurahan',
    ];

    protected $appends =
    [
        'responsible',
        'location',
        'user',
        'is_deletable',
        'is_updatable',
    ];

    public function scopeApi(Builder $query, $user)
    {
        $path = config('filesystems.assets.esatgas') . "sarana_operasi/apar/";
        $query->selectRaw("id_apar AS id,keterangan AS note");
        $query->selectRaw("CONCAT('" . $path . "', tautan_foto) AS image");
        $query->selectRaw("sarop_kehilangan_apar_new.*");
        $query->selectRaw("DATE_FORMAT(tgl_lahir, '%d %M %Y') AS birthdate");
        $query->selectRaw("'" . $user->nrk . "' AS user_login, '" . $user->kelurahan->id . "' AS user_kelurahan");
        if (!in_array($user->role_id, [1, 2, 3]) && !$user->bypass_area) {
            if (in_array($user->level->id, [99])) {
                $query->whereRaw("(no_pegawai = '" . $user->nrk . "' OR no_kelurahan = '" . $user->kelurahan->id . "')");
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
                    'keterangan',
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
            ->when($request->string('tahun'), function (Builder $query, string $value) use ($request) {
                if ($value) {
                    if ($value == 'thisyear') {
                        $query->whereRaw('tahun = YEAR(CURDATE())');
                    } else if ($value == 'lastyear') {
                        $query->whereRaw('tahun = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))');
                    } else if ($value == 'yearrange' && $request->string('start_tahun') and $request->string('end_tahun')) {
                        $query->whereRaw('tahun BETWEEN ? AND ?', [$request->string('start_tahun'), $request->string('end_tahun')]);
                    }
                }
            })
            ->when($request->string('kota'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('no_kota', explode(',', $value));
                }
            })
            ->when($request->string('kecamatan'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('no_kecamatan', explode(',', $value));
                }
            })
            ->when($request->string('kelurahan'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('no_kelurahan', explode(',', $value));
                }
            })
            ->when($request->string('jenis_apar'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('jenis_apar', explode(',', $value));
                }
            })
            ->when($request->string('sort', 'latest'), function (Builder $query, string $value) {
                if ($value == 'oldest') {
                    $query->orderByRaw('created_at ASC');
                } else {
                    $query->orderByRaw('created_at DESC');
                }
            });
        return $query;
    }

    public function getResponsibleAttribute()
    {
        $data =
            [
                "nik" => (int)$this->nik,
                "birthplace" => (string)$this->tmpt_lahir,
                "birthdate" => (string)$this->birthdate,
                "name" => (string)$this->nm_penanggung_jawab,
                "phone" => (string)_maskPhone($this->no_telepon),
            ];
        return $data;
    }

    public function getLocationAttribute()
    {
        $address = _strip($this->alamat);
        $address .= "\n" . $this->nama_kelurahan . ", " . $this->nama_kecamatan . " - " . $this->nama_kota;
        $address .= "\nRT: " . $this->no_rt . " RW: " . $this->no_rw;
        $data =
            [
                "alamat" => (string)$address,
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
        return (($this->user_login == $this->no_pegawai or $this->user_kelurahan == $this->no_kelurahan));
    }

    public function getIsUpdatableAttribute()
    {
        return (($this->user_login == $this->no_pegawai or $this->user_kelurahan == $this->no_kelurahan));
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'tahun' => 'integer',
            'responsible' => 'array',
            'location' => 'array',
            'user' => 'array',
            'created_at' => 'datetime:d M Y H:i',
            'updated_at' => 'datetime:d M Y H:i',
            'is_deletable' => 'boolean',
            'is_updatable' => 'boolean',
        ];
    }
}
