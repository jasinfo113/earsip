<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EsatgasSosialisasiKhusus extends Model
{

    public $timestamps = false;

    protected $connection = 'esatgas';
    protected $table = 'sosialisasi_khusus';
    protected $primaryKey = 'no_sosialisasi_khusus';

    protected $fillable = [
        'no_sosialisasi_khusus',
        'kd_sosialisasi_khusus',
        'no_pegawai',
        'id_kategori',
        'nama_kategori',
        'tanggal',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'idrw',
        'rw',
        'rt',
        'tautan_foto',
        'keterangan',
        'status_sosialisasi',
        'no_atasan',
        'nama_atasan',
        'tanggal_validasi',
        'keterangan_validasi',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'no_sosialisasi_khusus',
        'kd_sosialisasi_khusus',
        'no_pegawai',
        'id_kategori',
        'nama_kategori',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'idrw',
        'rw',
        'rt',
        'tautan_foto',
        'keterangan',
        'status_sosialisasi',
        'no_atasan',
        'nama_atasan',
        'tanggal_validasi',
        'keterangan_validasi',
        'created_at',
        'updated_at',
        'is_deletable',
        'is_updatable',
        'is_rejectable',
        'is_verifiable',
        'user_login',
        'is_verificator',
        'items',
        'details',
    ];

    protected $appends =
    [
        'location',
        'details',
        'status',
        'user',
        'verification',
        'is_deletable',
        'is_updatable',
        'is_rejectable',
        'is_verifiable',
    ];

    public function scopeApi(Builder $query, $user)
    {
        $path = config('filesystems.assets.esatgas') . "sosialisasi_khusus/";
        $query->selectRaw("sosialisasi_khusus.no_sosialisasi_khusus AS id,sosialisasi_khusus.kd_sosialisasi_khusus AS `code`,sosialisasi_khusus.keterangan AS note");
        $query->selectRaw("CONCAT('" . $path . "', sosialisasi_khusus.tautan_foto) AS image");
        $query->selectRaw("sosialisasi_khusus.*");
        $query->selectRaw("'" . $user->nrk . "' AS user_login");
        if (in_array($user->role_id, [1, 2]) or $user->level->id == 16) {
            $query->selectRaw("'1' AS is_verificator");
        }
        if (!in_array($user->role_id, [1, 2, 3]) && !$user->bypass_area) {
            if (in_array($user->level->id, [99])) {
                $query->whereRaw("sosialisasi_khusus.no_pegawai = '" . $user->nrk . "'");
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
                    'sosialisasi_khusus.kd_sosialisasi_khusus',
                    'sosialisasi_khusus.keterangan',
                ], 'LIKE', "%" . $value . "%");
            }
        })
            ->when($request->string('period'), function (Builder $query, string $value) use ($request) {
                if ($value) {
                    if ($value == 'today') {
                        $query->whereRaw('DATE(sosialisasi_khusus.created_at) = CURDATE()');
                    } else if ($value == 'yesterday') {
                        $query->whereRaw('DATE(sosialisasi_khusus.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)');
                    } else if ($value == 'lastweek') {
                        $query->whereRaw('DATE(sosialisasi_khusus.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)');
                    } else if ($value == 'lastmonth') {
                        $query->whereRaw('DATE(sosialisasi_khusus.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)');
                    } else if ($value == 'daterange' && $request->string('start_date') and $request->string('end_date')) {
                        $query->whereRaw('DATE(sosialisasi_khusus.created_at) BETWEEN ? AND ?', [$request->string('start_date'), $request->string('end_date')]);
                    }
                }
            })
            ->when($request->string('status'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('sosialisasi_khusus.status_validasi', explode(',', $value));
                }
            })
            ->when($request->string('sort', 'latest'), function (Builder $query, string $value) {
                if ($value == 'oldest') {
                    $query->orderByRaw('sosialisasi_khusus.created_at ASC');
                } else {
                    $query->orderByRaw('sosialisasi_khusus.created_at DESC');
                }
            });
        return $query;
    }

    public function scopeHimbauan(Builder $query)
    {
        $query->whereRaw("sosialisasi_khusus.id_kategori = 2");
        return $query;
    }

    public function scopeStiker(Builder $query)
    {
        $query->selectRaw("GROUP_CONCAT(CONCAT(sosialisasi_khusus_detail.no_rw,'||',sosialisasi_khusus_detail.no_rt,'||',sosialisasi_khusus_detail.kecil,'||',sosialisasi_khusus_detail.besar,'||',IFNULL(sosialisasi_khusus_detail.keterangan,'')) ORDER BY sosialisasi_khusus_detail.no_rw,sosialisasi_khusus_detail.no_rt ASC SEPARATOR '__') AS items")
            ->join("sosialisasi_khusus_detail", "sosialisasi_khusus.no_sosialisasi_khusus", "sosialisasi_khusus_detail.sosialisasi_khusus_id")
            ->groupBy("sosialisasi_khusus.no_sosialisasi_khusus")
            ->whereRaw("sosialisasi_khusus.id_kategori = 1");
        return $query;
    }

    public function getLocationAttribute()
    {
        $address = $this->nama_kelurahan . ", " . $this->nama_kecamatan . " - " . $this->nama_kota;
        if ($this->id_kategori == 2) {
            $address .= "\nRT: " . $this->rw . " RW: " . $this->rt;
        }
        $data =
            [
                "alamat" => (string)$address,
            ];
        return $data;
    }

    public function getDetailsAttribute()
    {
        $data = [];
        if ($this->items) {
            foreach (explode("__", $this->items) as $r) {
                $exp = explode("||", $r);
                $data[] =
                    [
                        "rw" => (string)$exp[0],
                        "rt" => (string)$exp[1],
                        "kecil" => (int)$exp[2],
                        "besar" => (int)$exp[3],
                        "note" => (string)$exp[4],
                    ];
            }
        }
        return $data;
    }

    public function getStatusAttribute()
    {
        $id = $this->status_validasi;
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
        return ($this->user_login == $this->no_pegawai && $this->status_validasi == 'BELUMVALIDASI');
    }

    public function getIsUpdatableAttribute()
    {
        return ($this->user_login == $this->no_pegawai && $this->status_validasi != 'DISETUJUI');
    }

    public function getIsRejectableAttribute()
    {
        return (isset($this->is_verificator) && $this->status_validasi == 'BELUMVALIDASI');
    }

    public function getIsVerifiableAttribute()
    {
        return (isset($this->is_verificator) && $this->status_validasi == 'BELUMVALIDASI');
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'tanggal' => 'date:d F Y',
            'location' => 'array',
            'details' => 'array',
            'status' => 'array',
            'user' => 'array',
            'verification' => 'array',
            'created_at' => 'datetime:d M Y H:i',
            'updated_at' => 'datetime:d M Y H:i',
            'is_deletable' => 'boolean',
            'is_updatable' => 'boolean',
            'is_rejectable' => 'boolean',
            'is_verifiable' => 'boolean',
        ];
    }
}
