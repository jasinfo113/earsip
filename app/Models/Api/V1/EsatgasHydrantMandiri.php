<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EsatgasHydrantMandiri extends Model
{

    public $timestamps = false;

    protected $connection = 'esatgas';
    protected $table = 'sarop_hydrant_mandiri';
    protected $primaryKey = 'id_hydrant_mandiri';

    protected $fillable = [
        'id_hydrant_mandiri',
        'kd_hydrant_mandiri',
        'tautan_foto',
        'sumber_aset',
        'tahun_pengadaan',
        'kondisi_unit',
        'lampu_penerangan_dalam_baik',
        'lampu_penerangan_dalam_rusak',
        'lampu_penerangan_dalam_keterangan',
        'lampu_penerangan_luar_baik',
        'lampu_penerangan_luar_rusak',
        'lampu_penerangan_luar_keterangan',
        'cat_bangunan',
        'cat_bangunan_keterangan',
        'daya_listrik',
        'daya_listrik_keterangan',
        'instalasi_listrik',
        'instalasi_listrik_keterangan',
        'apar_baik',
        'apar_rusak',
        'apar_keterangan',
        'bahan_bakar',
        'bahan_bakar_keterangan',
        'accu',
        'accu_keterangan',
        'pengukur_tekanan_air',
        'pengukur_tekanan_air_keterangan',
        'radiator',
        'radiator_keterangan',
        'lampu_lampu_indikator_baik',
        'lampu_lampu_indikator_rusak',
        'lampu_lampu_indikator_keterangan',
        'stater',
        'stater_keterangan',
        'kondisi_pompa',
        'kondisi_pompa_keterangan',
        'instalasi_pompa',
        'instalasi_pompa_keterangan',
        'kondisi_tandon',
        'kondisi_tandon_keterangan',
        'tutup',
        'tutup_keterangan',
        'volume',
        'volume_keterangan',
        'kebersihan_dalam_tandon',
        'kebersihan_dalam_tandon_keterangan',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'idrw',
        'no_rw',
        'no_rt',
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
        'id_hydrant_mandiri',
        'kd_hydrant_mandiri',
        'tautan_foto',
        'kondisi_unit',
        'lampu_penerangan_dalam_baik',
        'lampu_penerangan_dalam_rusak',
        'lampu_penerangan_dalam_keterangan',
        'lampu_penerangan_luar_baik',
        'lampu_penerangan_luar_rusak',
        'lampu_penerangan_luar_keterangan',
        'cat_bangunan',
        'cat_bangunan_keterangan',
        'daya_listrik',
        'daya_listrik_keterangan',
        'instalasi_listrik',
        'instalasi_listrik_keterangan',
        'apar_baik',
        'apar_rusak',
        'apar_keterangan',
        'bahan_bakar',
        'bahan_bakar_keterangan',
        'accu',
        'accu_keterangan',
        'pengukur_tekanan_air',
        'pengukur_tekanan_air_keterangan',
        'radiator',
        'radiator_keterangan',
        'lampu_lampu_indikator_baik',
        'lampu_lampu_indikator_rusak',
        'lampu_lampu_indikator_keterangan',
        'stater',
        'stater_keterangan',
        'kondisi_pompa',
        'kondisi_pompa_keterangan',
        'instalasi_pompa',
        'instalasi_pompa_keterangan',
        'kondisi_tandon',
        'kondisi_tandon_keterangan',
        'tutup',
        'tutup_keterangan',
        'volume',
        'volume_keterangan',
        'kebersihan_dalam_tandon',
        'kebersihan_dalam_tandon_keterangan',
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
        'details',
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
        $path = config('filesystems.assets.esatgas') . "sarana_operasi/hydrant_mandiri/";
        $query->selectRaw("id_hydrant_mandiri AS id,kd_hydrant_mandiri AS `code`");
        $query->selectRaw("CONCAT('" . $path . "', tautan_foto) AS image");
        $query->selectRaw("sarop_hydrant_mandiri.*");
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
                    'kd_hydrant_mandiri',
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
        $attributes = [];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Lampu Dalam",
                "baik" => (int)$this->lampu_penerangan_dalam_baik,
                "rusak" => (int)$this->lampu_penerangan_dalam_rusak,
                "note" => (string)_strip($this->lampu_penerangan_dalam_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Lampu Luar",
                "baik" => (int)$this->lampu_penerangan_luar_baik,
                "rusak" => (int)$this->lampu_penerangan_luar_rusak,
                "note" => (string)_strip($this->lampu_penerangan_luar_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Lampu Indikator",
                "baik" => (int)$this->lampu_lampu_indikator_baik,
                "rusak" => (int)$this->lampu_lampu_indikator_rusak,
                "note" => (string)_strip($this->lampu_lampu_indikator_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Cat",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->cat_bangunan),
                    "label" => (string)($this->cat_bangunan == "bagus" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->cat_bangunan_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Daya Listrik",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->daya_listrik),
                    "label" => (string)($this->daya_listrik == "ada" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->daya_listrik_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Instalasi Listrik",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->instalasi_listrik),
                    "label" => (string)($this->instalasi_listrik == "ada" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->instalasi_listrik_keterangan),
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
                "type" => "condition",
                "name" => "Bahan Bakar",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->bahan_bakar),
                    "label" => (string)($this->bahan_bakar == "ada" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->bahan_bakar_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "ACCU",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->accu),
                    "label" => (string)($this->accu == "ada" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->accu_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Pengukur Tekanan Air",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->pengukur_tekanan_air),
                    "label" => (string)($this->pengukur_tekanan_air == "berfungsi" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->pengukur_tekanan_air_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Radiator",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->radiator),
                    "label" => (string)($this->radiator == "ada" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->radiator_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Stater",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->stater),
                    "label" => (string)($this->stater == "berfungsi" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->stater_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Kondisi Pompa",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->kondisi_pompa),
                    "label" => (string)($this->kondisi_pompa == "berfungsi" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->kondisi_pompa_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Instalasi Pompa",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->instalasi_pompa),
                    "label" => (string)($this->instalasi_pompa == "ada" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->instalasi_pompa_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Tandon",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->kondisi_tandon),
                    "label" => (string)($this->kondisi_tandon == "baik" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->kondisi_tandon_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Kebersihan Dalam Tandon",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->kebersihan_dalam_tandon),
                    "label" => (string)($this->kebersihan_dalam_tandon == "bersih" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->kebersihan_dalam_tandon_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Tutup",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->tutup),
                    "label" => (string)($this->tutup == "baik" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->tutup_keterangan),
            ];
        $attributes[] =
            [
                "type" => "condition",
                "name" => "Volume",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->volume),
                    "label" => (string)($this->volume == "bagus" ? "success" : "danger"),
                ],
                "note" => (string)_strip($this->volume_keterangan),
            ];
        return $attributes;
    }

    public function getDetailsAttribute()
    {
        $attributes = [];
        $details = _getData("esatgas", "sarop_hydrant_mandiri_detail", "*", "id_hydrant_mandiri = '" . $this->id_hydrant_mandiri . "'");
        if ($details) {
            foreach ($details as $detail) {
                $attributes[] =
                    [
                        "type" => "info",
                        "name" => "Nomor Box",
                        "value" => (int)$detail->box,
                    ];
                $attributes[] =
                    [
                        "type" => "condition",
                        "name" => "Kondisi Box",
                        "condition" =>
                        [
                            "name" => (string)strtoupper($detail->kondisi_box_hydrant),
                            "label" => (string)($detail->kondisi_box_hydrant == "baik" ? "success" : "danger"),
                        ],
                        "note" => (string)_strip($detail->kondisi_box_hydrant_keterangan),
                    ];
                $attributes[] =
                    [
                        "type" => "condition",
                        "name" => "Kunci Box",
                        "condition" =>
                        [
                            "name" => (string)strtoupper($detail->kunci_box_hydrant),
                            "label" => (string)($detail->kunci_box_hydrant == "ada" ? "success" : "danger"),
                        ],
                        "note" => (string)_strip($detail->kunci_box_hydrant_keterangan),
                    ];
                $attributes[] =
                    [
                        "type" => "condition",
                        "name" => "Cat",
                        "condition" =>
                        [
                            "name" => (string)strtoupper($detail->cat_box_hydrant),
                            "label" => (string)($detail->cat_box_hydrant == "bagus" ? "success" : "danger"),
                        ],
                        "note" => (string)_strip($detail->cat_box_hydrant_keterangan),
                    ];
                $attributes[] =
                    [
                        "type" => "value",
                        "name" => "Selang",
                        "baik" => (int)$detail->selang_baik,
                        "rusak" => (int)$detail->selang_rusak,
                        "note" => (string)_strip($detail->selang_keterangan),
                    ];
                $attributes[] =
                    [
                        "type" => "value",
                        "name" => "Nozzle",
                        "baik" => (int)$detail->nozzle_baik,
                        "rusak" => (int)$detail->nozzle_rusak,
                        "note" => (string)_strip($detail->nozzle_keterangan),
                    ];
            }
        }
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
