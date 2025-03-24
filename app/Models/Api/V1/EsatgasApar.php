<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EsatgasApar extends Model
{

    public $timestamps = false;

    protected $connection = 'esatgas';
    protected $table = 'sarop_apar';
    protected $primaryKey = 'id_apar';

    protected $fillable = [
        'id_apar',
        'kd_apar',
        'sumber_aset',
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
        'no_apar',
        'jenis_apar',
        'kondisi',
        'ukuran',
        'merek',
        'tahun',
        'isi_tabung',
        'tekanan',
        'jumlah',
        'tautan_foto',
        'latitude',
        'longitude',
        'keterangan',
        'status_validasi',
        'no_pegawai',
        'no_atasan',
        'nama_atasan',
        'tanggal_validasi',
        'keterangan_validasi',
        'created_at',
        'created_from',
        'created_by',
        'updated_at',
        'updated_from',
        'updated_by',
        'is_deleted',
        'deleted_at',
        'deleted_from',
        'deleted_by',
    ];

    protected $hidden = [
        'id_apar',
        'kd_apar',
        'no_apar',
        'tautan_foto',
        'nik',
        'tmpt_lahir',
        'tgl_lahir',
        'nm_penanggung_jawab',
        'no_telepon',
        'kondisi',
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
        'latitude',
        'longitude',
        'status_validasi',
        'no_pegawai',
        'no_atasan',
        'nama_atasan',
        'tanggal_validasi',
        'keterangan_validasi',
        'created_from',
        'created_by',
        'updated_at',
        'updated_from',
        'updated_by',
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
        'responsible',
        'location',
        'condition',
        'status',
        'user',
        'verification',
        'is_deletable',
        'is_updatable',
        'is_rejectable',
        'is_verifiable',
        'is_updated',
    ];

    public function scopeApi(Builder $query, $user)
    {
        $path = config('filesystems.assets.esatgas') . "sarana_operasi/apar/";
        $query->selectRaw("id_apar AS id,kd_apar AS `code`,no_apar AS number,keterangan AS note");
        $query->selectRaw("CONCAT('" . $path . "', tautan_foto) AS image");
        $query->selectRaw("sarop_apar.*");
        $query->selectRaw("DATE_FORMAT(sarop_apar.tgl_lahir, '%d %M %Y') AS birthdate");
        $query->selectRaw("'" . $user->nrk . "' AS user_login, '" . $user->kelurahan->id . "' AS user_kelurahan");
        if (in_array($user->role_id, [1, 2]) or $user->level->id == 16) {
            $query->selectRaw("'1' AS is_verificator");
        }
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
        $query->whereRaw("sarop_apar.is_deleted = 0");
        return $query;
    }

    public function scopeFilter(Builder $query, Request $request)
    {
        $query->when($request->string('search'), function (Builder $query, string $value) {
            if ($value) {
                $query->whereAny([
                    'kd_apar',
                    'no_apar',
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
            ->when($request->string('sumber_aset'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('sumber_aset', explode(',', $value));
                }
            })
            ->when($request->string('kondisi'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('kondisi', explode(',', $value));
                }
            })
            ->when($request->string('jenis_apar'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('jenis_apar', explode(',', $value));
                }
            })
            ->when($request->string('ukuran'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('ukuran', explode(',', $value));
                }
            })
            ->when($request->string('isi_tabung'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('isi_tabung', explode(',', $value));
                }
            })
            ->when($request->string('tekanan'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('tekanan', explode(',', $value));
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

    public function scopeSummary(Builder $query)
    {
        $path = config('filesystems.assets.esatgas') . "sarana_operasi/apar/";
        $query->selectRaw("id_apar AS id,kd_apar AS `code`,no_apar AS number,keterangan AS note");
        $query->selectRaw("CONCAT('" . $path . "', tautan_foto) AS image");
        $query->selectRaw("sarop_apar.*");
        $query->selectRaw("DATE_FORMAT(sarop_apar.tgl_lahir, '%d %M %Y') AS birthdate");
        $query->selectRaw("'-1' AS user_login, '-1' AS user_kelurahan");
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
                "latitude" => (float)$this->latitude,
                "longitude" => (float)$this->longitude,
            ];
        return $data;
    }

    public function getConditionAttribute()
    {
        $data =
            [
                "name" => (string)$this->kondisi,
                "label" => (string)($this->kondisi == "BAIK" ? "success" : "danger"),
            ];
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

    public function getIsUpdatedAttribute()
    {
        $barcode = $this->no_apar;
        if ($barcode and strlen($barcode) > 3) {
            $where = "REGEXP_REPLACE(TRIM(no_apar), '[[:space:]]+', '') = REGEXP_REPLACE(TRIM('" . $barcode . "'), '[[:space:]]+', '') AND is_deleted = 0";
            $row = _singleData("esatgas", "sarop_apar_new", "id_apar", $where);
            if (!$row) {
                return false;
            }
        }
        return true;
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'tahun' => 'integer',
            'jumlah' => 'integer',
            'responsible' => 'array',
            'location' => 'array',
            'status' => 'array',
            'is_hilang' => 'boolean',
            'user' => 'array',
            'created_at' => 'datetime:d M Y H:i',
            'updated_at' => 'datetime:d M Y H:i',
            'is_deletable' => 'boolean',
            'is_updatable' => 'boolean',
            'is_rejectable' => 'boolean',
            'is_verifiable' => 'boolean',
            'is_updated' => 'boolean',
        ];
    }
}
