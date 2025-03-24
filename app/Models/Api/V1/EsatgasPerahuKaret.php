<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EsatgasPerahuKaret extends Model
{

    public $timestamps = false;

    protected $connection = 'esatgas';
    protected $table = 'sarop_perahu_karet';
    protected $primaryKey = 'id_perahu_karet';

    protected $fillable = [
        'id_perahu_karet',
        'kd_perahu_karet',
        'tautan_foto',
        'kondisi_unit',
        'sumber_aset',
        'tahun_pengadaan',
        'dayung_baik',
        'dayung_rusak',
        'dayung_keterangan',
        'perahu_karet_baik',
        'perahu_karet_rusak',
        'perahu_karet_keterangan',
        'motor_tempel_baik',
        'motor_tempel_rusak',
        'motor_tempel_keterangan',
        'helm_baik',
        'helm_rusak',
        'helm_keterangan',
        'ring_bouy_baik',
        'ring_bouy_rusak',
        'ring_bouy_keterangan',
        'pelampung_baik',
        'pelampung_rusak',
        'pelampung_keterangan',
        'pompa_perahu_baik',
        'pompa_perahu_rusak',
        'pompa_perahu_keterangan',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'no_rt',
        'idrw',
        'no_rw',
        'latitude',
        'longitude',
        'status_validasi',
        'no_atasan',
        'nama_atasan',
        'tanggal_validasi',
        'keterangan_validasi',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $hidden = [
        'id_perahu_karet',
        'kd_perahu_karet',
        'tautan_foto',
        'kondisi_unit',
        'dayung_baik',
        'dayung_rusak',
        'dayung_keterangan',
        'perahu_karet_baik',
        'perahu_karet_rusak',
        'perahu_karet_keterangan',
        'motor_tempel_baik',
        'motor_tempel_rusak',
        'motor_tempel_keterangan',
        'helm_baik',
        'helm_rusak',
        'helm_keterangan',
        'ring_bouy_baik',
        'ring_bouy_rusak',
        'ring_bouy_keterangan',
        'pelampung_baik',
        'pelampung_rusak',
        'pelampung_keterangan',
        'pompa_perahu_baik',
        'pompa_perahu_rusak',
        'pompa_perahu_keterangan',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'no_rt',
        'idrw',
        'no_rw',
        'latitude',
        'longitude',
        'status_validasi',
        'no_atasan',
        'nama_atasan',
        'tanggal_validasi',
        'keterangan_validasi',
        'created_by',
        'updated_by',
        'infos',
        'updated_at',
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
        'location',
        'condition',
        'infos',
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
        $path = config('filesystems.assets.esatgas') . "sarana_operasi/perahu_karet/";
        $query->selectRaw("id_perahu_karet AS id,kd_perahu_karet AS `code`");
        $query->selectRaw("CONCAT('" . $path . "', tautan_foto) AS image");
        $query->selectRaw("sarop_perahu_karet.*");
        $query->selectRaw("'" . $user->nrk . "' AS user_login, '" . $user->kelurahan->id . "' AS user_kelurahan");
        if (in_array($user->role_id, [1, 2]) or $user->level->id == 16) {
            $query->selectRaw("'1' AS is_verificator");
        }
        if (!in_array($user->role_id, [1, 2, 3]) && !$user->bypass_area) {
            if (in_array($user->level->id, [99])) {
                $query->whereRaw("(created_by = '" . $user->nrk . "' OR no_kelurahan = '" . $user->kelurahan->id . "')");
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
                    'kd_perahu_karet',
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
                        $query->whereRaw('tahun_pengadaan = YEAR(CURDATE())');
                    } else if ($value == 'lastyear') {
                        $query->whereRaw('tahun_pengadaan = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))');
                    } else if ($value == 'yearrange' && $request->string('start_tahun') and $request->string('end_tahun')) {
                        $query->whereRaw('tahun_pengadaan BETWEEN ? AND ?', [$request->string('start_tahun'), $request->string('end_tahun')]);
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
            ->when($request->string('sumber_aset'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('sumber_aset', explode(',', $value));
                }
            })
            ->when($request->string('kondisi'), function (Builder $query, string $value) {
                if ($value) {
                    if ($value == "BAIK") {
                        $where = "kondisi_unit = 1";
                    } else {
                        $where = "kondisi_unit = 2";
                    }
                    $query->whereRaw($where);
                }
            })
            ->when($request->string('status'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('status_validasi', explode(',', $value));
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

    public function getLocationAttribute()
    {
        $address = $this->nama_kelurahan . ", " . $this->nama_kecamatan . " - " . $this->nama_kota;
        $address .= "\nRT: " . $this->no_rt . " RW: " . $this->no_rw;
        $data =
            [
                "alamat" => (string)$address,
                "latitude" => (float)$this->latitude,
                "longitude" => (float)$this->longitude,
            ];
        return $data;
    }

    public function getConditionAttribute()
    {
        $data =
            [
                "name" => (string)($this->kondisi_unit == 1 ? "BAIK" : "RUSAK"),
                "label" => (string)($this->kondisi_unit == 1 ? "success" : "danger"),
            ];
        return $data;
    }

    public function getInfosAttribute()
    {
        $attributes[] =
            [
                "type" => "value",
                "name" => "Dayung",
                "baik" => (int)$this->dayung_baik,
                "rusak" => (int)$this->dayung_rusak,
                "note" => (string)_strip($this->dayung_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Perahu Karet",
                "baik" => (int)$this->perahu_karet_baik,
                "rusak" => (int)$this->perahu_karet_rusak,
                "note" => (string)_strip($this->perahu_karet_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Motor Tempel",
                "baik" => (int)$this->motor_tempel_baik,
                "rusak" => (int)$this->motor_tempel_rusak,
                "note" => (string)_strip($this->motor_tempel_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Helm",
                "baik" => (int)$this->helm_baik,
                "rusak" => (int)$this->helm_rusak,
                "note" => (string)_strip($this->helm_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Ring Bouy",
                "baik" => (int)$this->ring_bouy_baik,
                "rusak" => (int)$this->ring_bouy_rusak,
                "note" => (string)_strip($this->ring_bouy_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Pelampung",
                "baik" => (int)$this->pelampung_baik,
                "rusak" => (int)$this->pelampung_rusak,
                "note" => (string)_strip($this->pelampung_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Pompa Perahu",
                "baik" => (int)$this->pompa_perahu_baik,
                "rusak" => (int)$this->pompa_perahu_rusak,
                "note" => (string)_strip($this->pompa_perahu_keterangan),
            ];
        return $attributes;
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
        if ($this->created_by) {
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
        return (($this->user_login == $this->no_pegawai or $this->user_kelurahan == $this->no_kelurahan) && $this->status_validasi == 'BELUMVALIDASI');
    }

    public function getIsUpdatableAttribute()
    {
        return (($this->user_login == $this->no_pegawai or $this->user_kelurahan == $this->no_kelurahan) && $this->status_validasi != 'DITOLAK');
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
            'tahun_pengadaan' => 'integer',
            'location' => 'array',
            'infos' => 'array',
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
