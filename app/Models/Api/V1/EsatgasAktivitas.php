<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use App\Models\Api\V1\EsatgasApar;
use App\Models\Api\V1\EsatgasAparNew;

class EsatgasAktivitas extends Model
{

    public $timestamps = false;

    protected $connection = 'esatgas';
    protected $table = 'aktivitas';
    protected $primaryKey = 'no_aktivitas';

    protected $fillable = [
        'no_aktivitas',
        'no_pegawai',
        'no_daftar_aktivitas',
        'kd_aktivitas',
        'tanggal',
        'jam_awal',
        'jam_akhir',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'keterangan',
        'status_aktivitas',
        'no_atasan',
        'nama_atasan',
        'tanggal_validasi',
        'keterangan_validasi',
        'modul',
        'modul_id',
        'created_at',
        'created_from',
        'updated_at',
        'updated_from',
        'is_deleted',
        'deleted_at',
        'deleted_from',
        'deleted_by',
    ];

    protected $hidden = [
        'no_aktivitas',
        'no_pegawai',
        'no_daftar_aktivitas',
        'kd_aktivitas',
        'tanggal',
        'jam_awal',
        'jam_akhir',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'keterangan',
        'status_aktivitas',
        'no_atasan',
        'nama_atasan',
        'tanggal_validasi',
        'keterangan_validasi',
        'modul',
        'modul_id',
        'nama_aktivitas',
        'created_from',
        'updated_at',
        'updated_from',
        'is_deleted',
        'deleted_at',
        'deleted_from',
        'deleted_by',
        'is_deletable',
        'is_updatable',
        'is_rejectable',
        'is_verifiable',
        'user_login',
        'user_kelurahan',
        'is_verificator',
    ];

    protected $appends =
    [
        'activity',
        'location',
        'reference',
        'status',
        'user',
        'verification',
        'is_deletable',
        'is_updatable',
        'is_rejectable',
        'is_verifiable',
        'show_confirm',
        'apar',
    ];

    public function scopeApi(Builder $query, $user)
    {
        $query->selectRaw("aktivitas.no_aktivitas AS id,aktivitas.kd_aktivitas AS `code`,aktivitas.keterangan AS note");
        $query->selectRaw("CONCAT(DATE_FORMAT(aktivitas.tanggal, '%d %M %Y'),IF(aktivitas.jam_awal != '00:00:00', CONCAT(' ', LEFT(aktivitas.jam_awal,5), ' - ', LEFT(aktivitas.jam_akhir,5)),'')) AS `period`");
        $query->selectRaw("CONCAT(aktivitas.tanggal, ' ', aktivitas.jam_awal) AS `start`,CONCAT(aktivitas.tanggal, ' ', aktivitas.jam_akhir) AS `end`");
        $query->selectRaw("CONCAT(FLOOR(TIME_TO_SEC(TIMEDIFF(aktivitas.jam_akhir,aktivitas.jam_awal)) / 60), ' menit') AS `duration`");
        $query->selectRaw("aktivitas.*");
        $query->selectRaw("daftar_aktivitas.nama_aktivitas");
        $query->selectRaw("'" . $user->nrk . "' AS user_login, '" . $user->kelurahan->id . "' AS user_kelurahan");
        $query->join("daftar_aktivitas", "aktivitas.no_daftar_aktivitas", "daftar_aktivitas.no_daftar_aktivitas");
        if (in_array($user->role_id, [1, 2]) or $user->level->id == 16) {
            $query->selectRaw("'1' AS is_verificator");
        }
        if (!in_array($user->role_id, [1, 2, 3]) && !$user->bypass_area) {
            if (in_array($user->level->id, [99])) {
                $query->whereRaw("IF(aktivitas.modul != '' AND aktivitas.modul != 'aktivitas' AND aktivitas.modul_id > 0,(aktivitas.no_pegawai = '" . $user->nrk . "' OR aktivitas.no_kelurahan = '" . $user->kelurahan->id . "'),aktivitas.no_pegawai = '" . $user->nrk . "')");
            } else {
                $pos_id = $user->pos->id;
                /*
                $exp = strlen($pos_id);
                if ($exp > 4) {
                    $query->whereRaw("no_kelurahan = '" . $user->kelurahan->id . "'");
                } else if ($exp > 2) {
                    $query->whereRaw("no_kecamatan = '" . $user->kecamatan->id . "'");
                } else {
                    $query->whereRaw("no_kota = '" . $user->kota->id . "'");
                }
                */
                $where_area = NULL;
                if (substr_count($pos_id, ".") >= 2) {
                    // user pos
                    $where_area = "aktivitas.no_kelurahan IN(" . implode(",", array_map("intval", $user->villages)) . ")";
                } else if (substr_count($pos_id, ".") >= 1) {
                    $exp = explode(".", $pos_id);
                    if ($exp[0] == "0" && $user->kode_level != 11) {
                        // user dinas
                    } else if (strlen($exp[1]) == 1) {
                        // user sudin
                        $where_area = "aktivitas.no_kota IN(" . implode(",", array_map("intval", $user->cities)) . ")";
                    } else {
                        // user sektor
                        $where_area = "aktivitas.no_kecamatan IN(" . implode(",", array_map("intval", $user->districts)) . ")";
                    }
                }
                if (isset($where_area)) {
                    $query->whereRaw($where_area);
                }
            }
        }
        $query->whereRaw("aktivitas.is_deleted = 0");
        return $query;
    }

    public function scopeFilter(Builder $query, Request $request)
    {
        $query->when($request->string('search'), function (Builder $query, string $value) {
            if ($value) {
                $query->whereAny([
                    'aktivitas.kd_aktivitas',
                    'aktivitas.keterangan',
                ], 'LIKE', "%" . $value . "%");
            }
        })
            ->when($request->string('period'), function (Builder $query, string $value) use ($request) {
                if ($value) {
                    if ($value == 'today') {
                        $query->whereRaw('DATE(aktivitas.tanggal) = CURDATE()');
                    } else if ($value == 'yesterday') {
                        $query->whereRaw('DATE(aktivitas.tanggal) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)');
                    } else if ($value == 'lastweek') {
                        $query->whereRaw('DATE(aktivitas.tanggal) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)');
                    } else if ($value == 'lastmonth') {
                        $query->whereRaw('DATE(aktivitas.tanggal) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)');
                    } else if ($value == 'daterange' && $request->string('start_date') and $request->string('end_date')) {
                        $query->whereRaw('DATE(aktivitas.tanggal) BETWEEN ? AND ?', [$request->string('start_date'), $request->string('end_date')]);
                    }
                }
            })
            ->when($request->integer('no_pegawai'), function (Builder $query, int $value) {
                if ($value) {
                    $query->where('aktivitas.no_pegawai', $value);
                }
            })
            ->when($request->integer('no_aktivitas'), function (Builder $query, int $value) {
                if ($value) {
                    $query->where('aktivitas.no_daftar_aktivitas', $value);
                }
            })
            ->when($request->string('reference'), function (Builder $query, string $value) {
                if ($value) {
                    $query->where('aktivitas.modul', $value);
                }
            })
            ->when($request->string('status'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('aktivitas.status_aktivitas', explode(',', $value));
                }
            })
            ->when($request->string('sort', 'latest'), function (Builder $query, string $value) {
                if ($value == 'oldest') {
                    $query->orderByRaw('aktivitas.created_at ASC');
                } else {
                    $query->orderByRaw('aktivitas.created_at DESC');
                }
            });
        return $query;
    }

    public function scopeApar(Builder $query, Request $request)
    {
        $ref = $request->string('ref');
        $ref_id = $request->integer('ref_id');
        $year = $request->integer('year');
        $table_apar = ($request->has('show_ref') ? "sarop_apar" : "sarop_apar_new");
        $query->leftJoin($table_apar, function (JoinClause $join) use ($table_apar) {
            $join->on("aktivitas.modul_id", $table_apar . ".id_apar")
                ->whereRaw("aktivitas.modul = 'apar'");
        });
        if ($ref == "rw") {
            $query->whereRaw($table_apar . ".idrw = '" . $ref_id . "'");
        } else if ($ref == "kelurahan") {
            $query->whereRaw($table_apar . ".no_kelurahan = '" . $ref_id . "'");
        } else if ($ref == "kecamatan") {
            $query->whereRaw($table_apar . ".no_kecamatan = '" . $ref_id . "'");
        } else {
            $query->whereRaw($table_apar . ".no_kota = '" . $ref_id . "'");
        }
        if ($request->has("apar_status")) {
            $apar_status = $request->string("apar_status");
            if ($apar_status == "verified") {
                $query->whereRaw($table_apar . ".status_validasi = 'DISETUJUI'");
            } else if ($apar_status == "pending") {
                $query->whereRaw($table_apar . ".status_validasi = 'BELUMVALIDASI'");
            } else if ($apar_status == "no_qr") {
                $query->whereRaw("(" . $table_apar . ".no_apar IS NULL OR " . $table_apar . ".no_apar = '' OR LENGTH(" . $table_apar . ".no_apar) <= 3)");
            } else if ($apar_status == "has_qr") {
                $query->whereRaw("LENGTH(" . $table_apar . ".no_apar) > 3");
            }
        }
        if ($year > 0) {
            if ($year == 2017) {
                $query->whereRaw($table_apar . ".tahun <= '" . $year . "'");
            } else {
                $query->whereRaw($table_apar . ".tahun = '" . $year . "'");
            }
        }
        $query->whereRaw($table_apar . ".is_deleted = 0");
        $query->groupByRaw($table_apar . ".id_apar");
        return $query;
    }

    public function scopeUser(Builder $query, $user)
    {
        $query->whereRaw("aktivitas.no_pegawai = '" . $user->nrk . "'");
        return $query;
    }

    public function scopePending(Builder $query)
    {
        $query->whereRaw("LOWER(REPLACE(aktivitas.status_aktivitas, ' ', '')) = 'belumvalidasi'");
        return $query;
    }

    public function getAparAttribute()
    {
        if ($this->modul && $this->modul_id) {
            $module = $this->modul;
            if ($module == "apar") {
                if ($this->modul_id >= 10000) {
                    return EsatgasAparNew::summary()->find($this->modul_id);
                } else {
                    return EsatgasApar::summary()->find($this->modul_id);
                }
            }
        }
        return NULL;
    }

    public function getActivityAttribute()
    {
        $data =
            [
                "id" => (int)$this->no_daftar_aktivitas,
                "name" => (string)"Aktivitas #" . $this->no_daftar_aktivitas,
                "description" => (string)$this->nama_aktivitas,
            ];
        return $data;
    }

    public function getLocationAttribute()
    {
        $address = $this->nama_kelurahan . ", " . $this->nama_kecamatan . " - " . $this->nama_kota;
        $data =
            [
                "alamat" => (string)$address,
            ];
        return $data;
    }

    public function getReferenceAttribute()
    {
        if ($this->modul && $this->modul_id) {
            $module = $this->modul;
            if ($module != "aktivitas") {
                $ref =
                    [
                        "id" => (int)$this->modul_id,
                        "name" => (string)$this->modul,
                        "description" => (string)(in_array($this->modul, ["apar", "skkl"]) ? strtoupper($this->modul) : ucwords(str_replace("_", " ", $this->modul))),
                        "label" => (string)"esatgas",
                    ];
                return json_decode(json_encode($ref));
            }
        }
        return NULL;
    }

    public function getStatusAttribute()
    {
        $id = $this->status_aktivitas;
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

    public function getVerificationAttribute()
    {
        if ($this->no_atasan) {
            $user = NULL;
            $user = _pegawaiByNrk($this->no_atasan);
            $data =
                [
                    "date" => (string)($this->tanggal_validasi ? date('d F Y H:i', strtotime($this->tanggal_validasi)) : ""),
                    "note" => (string)_strip($this->keterangan_validasi),
                    "user" => $user,
                ];
            return $data;
        }
        return NULL;
    }

    public function getIsDeletableAttribute()
    {
        if ($this->modul && $this->modul_id && $this->modul != "aktivitas" && $this->modul_id > 0) {
            return (($this->user_login == $this->no_pegawai or $this->user_kelurahan == $this->no_kelurahan) && $this->status_aktivitas == 'BELUMVALIDASI');
        }
        return ($this->user_login == $this->no_pegawai && $this->status_aktivitas == 'BELUMVALIDASI');
    }

    public function getIsUpdatableAttribute()
    {
        if ($this->modul && $this->modul_id && $this->modul != "aktivitas" && $this->modul_id > 0) {
            if ($this->modul == "apar" && $this->modul_id < 10000) {
                $apar = _singleData("esatgas", "sarop_apar", "no_apar", "id_apar = '" . $this->modul_id . "'");
                if ($apar) {
                    $barcode = $apar->no_apar;
                    if ($barcode and strlen($barcode) > 3) {
                        $where = "REGEXP_REPLACE(TRIM(no_apar), '[[:space:]]+', '') = REGEXP_REPLACE(TRIM('" . $barcode . "'), '[[:space:]]+', '') AND is_deleted = 0";
                        $row = _singleData("esatgas", "sarop_apar_new", "id_apar", $where);
                        if (!$row) {
                            return true;
                        }
                    }
                }
                return false;
            }
            return (($this->user_login == $this->no_pegawai or $this->user_kelurahan == $this->no_kelurahan));
        }
        return ($this->user_login == $this->no_pegawai);
    }

    public function getIsRejectableAttribute()
    {
        if ($this->modul && $this->modul_id && $this->modul != "aktivitas" && $this->modul_id > 0) {
            return (isset($this->is_verificator) && in_array($this->status_aktivitas, ["BELUMVALIDASI", "DISETUJUI"]));
        }
        return (isset($this->is_verificator) && $this->status_aktivitas == 'BELUMVALIDASI');
    }

    public function getIsVerifiableAttribute()
    {
        if ($this->modul && $this->modul_id && $this->modul != "aktivitas" && $this->modul_id > 0) {
            return (isset($this->is_verificator) && in_array($this->status_aktivitas, ["BELUMVALIDASI", "DITOLAK"]));
        }
        return (isset($this->is_verificator) && $this->status_aktivitas == 'BELUMVALIDASI');
    }

    public function getShowConfirmAttribute()
    {
        if ($this->modul && $this->modul_id && $this->modul != "aktivitas" && $this->modul_id > 0) {
            if ($this->modul == "apar") {
                $apar = $this->getAparAttribute();
                if ($apar && isset($apar->number) && strlen($apar->number) > 3) {
                    return true;
                }
                return false;
            }
            return true;
        }
        return false;
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'duration' => 'string',
            'location' => 'array',
            'status' => 'array',
            'user' => 'array',
            'verification' => 'array',
            'created_at' => 'datetime:d M Y H:i',
            'updated_at' => 'datetime:d M Y H:i',
        ];
    }
}
