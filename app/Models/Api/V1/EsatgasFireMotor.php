<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EsatgasFireMotor extends Model
{

    public $timestamps = false;

    protected $connection = 'esatgas';
    protected $table = 'sarop_fire_motor';
    protected $primaryKey = 'id_fire_motor';

    protected $fillable = [
        'id_fire_motor',
        'kd_fire_motor',
        'tautan_foto',
        'kondisi_unit',
        'sumber_aset',
        'tahun_pengadaan',
        'sepedah_baik',
        'sepedah_rusak',
        'sepedah_keterangan',
        'selang_hisap_baik',
        'selang_hisap_rusak',
        'selang_hisap_keterangan',
        'selang_penyalur_baik',
        'selang_penyalur_rusak',
        'selang_penyalur_keterangan',
        'pipa_portable_baik',
        'pipa_portable_rusak',
        'pipa_portable_keterangan',
        'nozzle_baik',
        'nozzle_rusak',
        'nozzle_keterangan',
        'pipa_cabang_baik',
        'pipa_cabang_rusak',
        'pipa_cabang_keterangan',
        'apar_baik',
        'apar_rusak',
        'apar_keterangan',
        'helm_baik',
        'helm_rusak',
        'helm_keterangan',
        'fire_jaket_baik',
        'fire_jaket_rusak',
        'fire_jaket_keterangan',
        'kunci_selang_hisap_baik',
        'kunci_selang_hisap_rusak',
        'kunci_selang_hisap_keterangan',
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
        'id_fire_motor',
        'kd_fire_motor',
        'tautan_foto',
        'kondisi_unit',
        'sepedah_baik',
        'sepedah_rusak',
        'sepedah_keterangan',
        'selang_hisap_baik',
        'selang_hisap_rusak',
        'selang_hisap_keterangan',
        'selang_penyalur_baik',
        'selang_penyalur_rusak',
        'selang_penyalur_keterangan',
        'pipa_portable_baik',
        'pipa_portable_rusak',
        'pipa_portable_keterangan',
        'nozzle_baik',
        'nozzle_rusak',
        'nozzle_keterangan',
        'pipa_cabang_baik',
        'pipa_cabang_rusak',
        'pipa_cabang_keterangan',
        'apar_baik',
        'apar_rusak',
        'apar_keterangan',
        'helm_baik',
        'helm_rusak',
        'helm_keterangan',
        'fire_jaket_baik',
        'fire_jaket_rusak',
        'fire_jaket_keterangan',
        'kunci_selang_hisap_baik',
        'kunci_selang_hisap_rusak',
        'kunci_selang_hisap_keterangan',
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
        $path = config('filesystems.assets.esatgas') . "sarana_operasi/fire_motor/";
        $query->selectRaw("id_fire_motor AS id,kd_fire_motor AS `code`");
        $query->selectRaw("CONCAT('" . $path . "', tautan_foto) AS image");
        $query->selectRaw("sarop_fire_motor.*");
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
                    'kd_fire_motor',
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
                "name" => "Sepeda",
                "baik" => (int)$this->sepedah_baik,
                "rusak" => (int)$this->sepedah_rusak,
                "note" => (string)_strip($this->sepedah_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Selang Hisap",
                "baik" => (int)$this->selang_hisap_baik,
                "rusak" => (int)$this->selang_hisap_rusak,
                "note" => (string)_strip($this->selang_hisap_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Selang Penyalur",
                "baik" => (int)$this->selang_penyalur_baik,
                "rusak" => (int)$this->selang_penyalur_rusak,
                "note" => (string)_strip($this->selang_penyalur_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Pipa Portable",
                "baik" => (int)$this->pipa_portable_baik,
                "rusak" => (int)$this->pipa_portable_rusak,
                "note" => (string)_strip($this->pipa_portable_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Nozzle",
                "baik" => (int)$this->nozzle_baik,
                "rusak" => (int)$this->nozzle_rusak,
                "note" => (string)_strip($this->nozzle_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Pipa Cabang",
                "baik" => (int)$this->pipa_cabang_baik,
                "rusak" => (int)$this->pipa_cabang_rusak,
                "note" => (string)_strip($this->pipa_cabang_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "APAR",
                "baik" => (int)$this->apar_baik,
                "rusak" => (int)$this->apar_rusak,
                "note" => (string)_strip($this->apar_keterangan),
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
                "name" => "Fire Jaket",
                "baik" => (int)$this->fire_jaket_baik,
                "rusak" => (int)$this->fire_jaket_rusak,
                "note" => (string)_strip($this->fire_jaket_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Kunci Selang Hisap",
                "baik" => (int)$this->kunci_selang_hisap_baik,
                "rusak" => (int)$this->kunci_selang_hisap_rusak,
                "note" => (string)_strip($this->kunci_selang_hisap_keterangan),
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
            return _pegawaiByNrk($this->created_by);
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
