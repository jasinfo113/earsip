<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EsatgasPompaPortable extends Model
{

    public $timestamps = false;

    protected $connection = 'esatgas';
    protected $table = 'sarop_pompa_portable';
    protected $primaryKey = 'id_pompa_portable';

    protected $fillable = [
        'id_pompa_portable',
        'kd_pompa_portable',
        'tautan_foto',
        'kondisi_unit',
        'sumber_aset',
        'tahun_pengadaan',
        'merek',
        'fungsi_unit',
        'pompa_baik',
        'pompa_rusak',
        'pompa_keterangan',
        'selang_penyalur_satu_lima_baik',
        'selang_penyalur_satu_lima_rusak',
        'selang_penyalur_satu_lima_keterangan',
        'selang_penyalur_dua_lima_baik',
        'selang_penyalur_dua_lima_rusak',
        'selang_penyalur_dua_lima_keterangan',
        'nozzle_satu_lima_baik',
        'nozzle_satu_lima_rusak',
        'nozzle_satu_lima_keterangan',
        'nozzle_dua_lima_baik',
        'nozzle_dua_lima_rusak',
        'nozzle_dua_lima_keterangan',
        'pipa_satu_lima_baik',
        'pipa_satu_lima_rusak',
        'pipa_satu_lima_keterangan',
        'pipa_dua_lima_baik',
        'pipa_dua_lima_rusak',
        'pipa_dua_lima_keterangan',
        'selang_penghisap_baik',
        'selang_penghisap_rusak',
        'selang_penghisap_keterangan',
        'kunci_selang_hisap_baik',
        'kunci_selang_hisap_rusak',
        'kunci_selang_hisap_keterangan',
        'bahan_bakar_baik',
        'bahan_bakar_rusak',
        'bahan_bakar_keterangan',
        'saringan_selang_hisap_baik',
        'saringan_selang_hisap_rusak',
        'saringan_selang_hisap_keterangan',
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
        'id_pompa_portable',
        'kd_pompa_portable',
        'tautan_foto',
        'kondisi_unit',
        'fungsi_unit',
        'pompa_baik',
        'pompa_rusak',
        'pompa_keterangan',
        'selang_penyalur_satu_lima_baik',
        'selang_penyalur_satu_lima_rusak',
        'selang_penyalur_satu_lima_keterangan',
        'selang_penyalur_dua_lima_baik',
        'selang_penyalur_dua_lima_rusak',
        'selang_penyalur_dua_lima_keterangan',
        'nozzle_satu_lima_baik',
        'nozzle_satu_lima_rusak',
        'nozzle_satu_lima_keterangan',
        'nozzle_dua_lima_baik',
        'nozzle_dua_lima_rusak',
        'nozzle_dua_lima_keterangan',
        'pipa_satu_lima_baik',
        'pipa_satu_lima_rusak',
        'pipa_satu_lima_keterangan',
        'pipa_dua_lima_baik',
        'pipa_dua_lima_rusak',
        'pipa_dua_lima_keterangan',
        'selang_penghisap_baik',
        'selang_penghisap_rusak',
        'selang_penghisap_keterangan',
        'kunci_selang_hisap_baik',
        'kunci_selang_hisap_rusak',
        'kunci_selang_hisap_keterangan',
        'bahan_bakar_baik',
        'bahan_bakar_rusak',
        'bahan_bakar_keterangan',
        'saringan_selang_hisap_baik',
        'saringan_selang_hisap_rusak',
        'saringan_selang_hisap_keterangan',
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
        $path = config('filesystems.assets.esatgas') . "sarana_operasi/pompa_portable/";
        $query->selectRaw("id_pompa_portable AS id,kd_pompa_portable AS `code`");
        $query->selectRaw("CONCAT('" . $path . "', tautan_foto) AS image");
        $query->selectRaw("sarop_pompa_portable.*");
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
                    'kd_pompa_portable',
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
                "type" => "condition",
                "name" => "Fungsi Unit",
                "condition" =>
                [
                    "name" => (string)strtoupper($this->fungsi_unit),
                    "label" => (string)(strtolower($this->fungsi_unit) == "baik" ? "success" : "danger"),
                ],
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Pompa",
                "baik" => (int)$this->pompa_baik,
                "rusak" => (int)$this->pompa_rusak,
                "note" => (string)_strip($this->pompa_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Selang Penyalur 1.5",
                "baik" => (int)$this->selang_penyalur_satu_lima_baik,
                "rusak" => (int)$this->selang_penyalur_satu_lima_rusak,
                "note" => (string)_strip($this->selang_penyalur_satu_lima_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Selang Penyalur 2.5",
                "baik" => (int)$this->selang_penyalur_dua_lima_baik,
                "rusak" => (int)$this->selang_penyalur_dua_lima_rusak,
                "note" => (string)_strip($this->selang_penyalur_dua_lima_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Nozzle 1.5",
                "baik" => (int)$this->nozzle_satu_lima_baik,
                "rusak" => (int)$this->nozzle_satu_lima_rusak,
                "note" => (string)_strip($this->nozzle_satu_lima_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Nozzle 2.5",
                "baik" => (int)$this->nozzle_dua_lima_baik,
                "rusak" => (int)$this->nozzle_dua_lima_rusak,
                "note" => (string)_strip($this->nozzle_dua_lima_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Pipa 1.5",
                "baik" => (int)$this->pipa_satu_lima_baik,
                "rusak" => (int)$this->pipa_satu_lima_rusak,
                "note" => (string)_strip($this->pipa_satu_lima_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Pipa 2.5",
                "baik" => (int)$this->pipa_dua_lima_baik,
                "rusak" => (int)$this->pipa_dua_lima_rusak,
                "note" => (string)_strip($this->pipa_dua_lima_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Selang Penghisap",
                "baik" => (int)$this->selang_penghisap_baik,
                "rusak" => (int)$this->selang_penghisap_rusak,
                "note" => (string)_strip($this->selang_penghisap_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Kunci Selang Hisap",
                "baik" => (int)$this->kunci_selang_hisap_baik,
                "rusak" => (int)$this->kunci_selang_hisap_rusak,
                "note" => (string)_strip($this->kunci_selang_hisap_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Saringan Selang Hisap",
                "baik" => (int)$this->saringan_selang_hisap_baik,
                "rusak" => (int)$this->saringan_selang_hisap_rusak,
                "note" => (string)_strip($this->saringan_selang_hisap_keterangan),
            ];
        $attributes[] =
            [
                "type" => "value",
                "name" => "Bahan Bakar",
                "baik" => (int)$this->bahan_bakar_baik,
                "rusak" => (int)$this->bahan_bakar_rusak,
                "note" => (string)_strip($this->bahan_bakar_keterangan),
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
