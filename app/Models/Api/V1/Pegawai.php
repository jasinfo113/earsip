<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $connection = 'central';
    protected $table = 'pegawai';
    protected $primaryKey = 'nip_nik';
    protected $keyType = 'string';

    protected $fillable = [
        'photo',
        'nama_pegawai',
        'no_telepon',
        'email',
        'password',
        'alamat_ktp',
        'alamat_domisili',
        'id_kelurahan_ktp',
        'id_kelurahan_domisili',
    ];

    protected $hidden = [
        'nip_nik',
        'nrk_id_pjlp',
        'nama_pegawai',
        'gelar_depan',
        'gelar_belakang',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'keterangan',
        'password',
        'role',
        'roles',
        'privileges',
        'id_jenis_pegawai',
        'id_agama',
        'id_pendidikan',
        'id_jurusan',
        'id_jabatan',
        'id_pangkat',
        'id_penugasan',
        'id_penempatan',
        'id_unit_kerja',
        'id_sub_unit_kerja',
        'id_group',
        'id_eselon',
        'id_status',
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
        'is_valid',
    ];

    protected $appends = [
        'nip',
        'nrk',
        'nama',
        'unit_kerja',
        'jabatan',
        'penugasan',
        'penempatan',
        'group',
        'id_provinsi_ktp',
        'id_kota_ktp',
        'id_kecamatan_ktp',
        'provinsi_ktp',
        'kota_ktp',
        'kecamatan_ktp',
        'kelurahan_ktp',
        'id_provinsi_domisili',
        'id_kota_domisili',
        'id_kecamatan_domisili',
        'provinsi_domisili',
        'kota_domisili',
        'kecamatan_domisili',
        'kelurahan_domisili',
        'bmi',
        'role',
        'roles',
        'privileges',
        'is_valid',
    ];


    public function getNipAttribute()
    {
        return (int)$this->nip_nik;
    }

    public function getNrkAttribute()
    {
        return (int)$this->nrk_id_pjlp;
    }

    public function getNamaAttribute()
    {
        return ($this->gelar_depan ? $this->gelar_depan . " " : "") . $this->nama_pegawai . ($this->gelar_belakang ? " " . $this->gelar_belakang : "");
    }

    public function getPhotoAttribute()
    {
        $photo = $this->attributes['photo'] ?? "";
        $photo_casual = _singleData("central", "pegawai_info", "photo_kasual AS photo", "nip_nik = '" . $this->nip_nik . "'")->photo ?? "";
        if ($photo_casual) {
            return _diskPathUrl('pegawai', $photo_casual, asset('assets/images/nophoto.png'));
        }
        return _diskPathUrl('pegawai', $photo, asset('assets/images/nophoto.png'));
    }

    public function getUnitKerjaAttribute()
    {
        $data = DB::connection('central')->table('m_pegawai_unit_kerja_sub')->select('nama_sub_unit_kerja')->where('id_sub_unit_kerja', $this->id_sub_unit_kerja)->first();
        $value = $data->nama_sub_unit_kerja ?? "";
        return $value;
    }

    public function getJabatanAttribute()
    {
        $data = DB::connection('central')->table('m_pegawai_jabatan')->select('nama_jabatan')->where('id_jabatan', $this->id_jabatan)->first();
        $value = $data->nama_jabatan ?? "";
        return $value;
    }

    public function getPenugasanAttribute()
    {
        $data = DB::connection('central')->table('m_pegawai_penugasan')->select('nama_penugasan')->where('id_penugasan', $this->id_penugasan)->first();
        $value = $data->nama_penugasan ?? "";
        return $value;
    }

    public function getPenempatanAttribute()
    {
        $data = DB::connection('central')->table('m_pegawai_penempatan')->select('nama_penempatan')->where('id_penempatan', $this->id_penempatan)->first();
        $value = $data->nama_penempatan ?? "";
        return $value;
    }

    public function getGroupAttribute()
    {
        $data = DB::connection('central')->table('m_pegawai_group')->select('nama_group')->where('id_group', $this->id_group)->first();
        $value = $data->nama_group ?? "";
        return $value;
    }

    public function getIdProvinsiKtpAttribute()
    {
        if ($this->id_kelurahan_ktp) {
            $id_kelurahan = $this->id_kelurahan_ktp;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            if ($id_kecamatan) {
                $id_kota = _singleData("central", "m_area_kecamatan", "id_kota AS id", "id_kecamatan = '" . $id_kecamatan . "'")->id ?? "";
                if ($id_kota) {
                    $id_provinsi = _singleData("central", "m_area_kota", "id_provinsi AS id", "id_kota = '" . $id_kota . "'")->id ?? "";
                    return $id_provinsi;
                }
            }
        }
        return "";
    }

    public function getIdKotaKtpAttribute()
    {
        if ($this->id_kelurahan_ktp) {
            $id_kelurahan = $this->id_kelurahan_ktp;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            if ($id_kecamatan) {
                $id_kota = _singleData("central", "m_area_kecamatan", "id_kota AS id", "id_kecamatan = '" . $id_kecamatan . "'")->id ?? "";
                return $id_kota;
            }
        }
        return "";
    }

    public function getIdKecamatanKtpAttribute()
    {
        if ($this->id_kelurahan_ktp) {
            $id_kelurahan = $this->id_kelurahan_ktp;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            return $id_kecamatan;
        }
        return "";
    }

    public function getIdProvinsiDomisiliAttribute()
    {
        if ($this->id_kelurahan_domisili) {
            $id_kelurahan = $this->id_kelurahan_domisili;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            if ($id_kecamatan) {
                $id_kota = _singleData("central", "m_area_kecamatan", "id_kota AS id", "id_kecamatan = '" . $id_kecamatan . "'")->id ?? "";
                if ($id_kota) {
                    $id_provinsi = _singleData("central", "m_area_kota", "id_provinsi AS id", "id_kota = '" . $id_kota . "'")->id ?? "";
                    return $id_provinsi;
                }
            }
        }
        return "";
    }

    public function getIdKotaDomisiliAttribute()
    {
        if ($this->id_kelurahan_domisili) {
            $id_kelurahan = $this->id_kelurahan_domisili;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            if ($id_kecamatan) {
                $id_kota = _singleData("central", "m_area_kecamatan", "id_kota AS id", "id_kecamatan = '" . $id_kecamatan . "'")->id ?? "";
                return $id_kota;
            }
        }
        return "";
    }

    public function getIdKecamatanDomisiliAttribute()
    {
        if ($this->id_kelurahan_domisili) {
            $id_kelurahan = $this->id_kelurahan_domisili;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            return $id_kecamatan;
        }
        return "";
    }

    public function getProvinsiKtpAttribute()
    {
        if ($this->id_kelurahan_ktp) {
            $id_kelurahan = $this->id_kelurahan_ktp;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            if ($id_kecamatan) {
                $id_kota = _singleData("central", "m_area_kecamatan", "id_kota AS id", "id_kecamatan = '" . $id_kecamatan . "'")->id ?? "";
                if ($id_kota) {
                    $id_provinsi = _singleData("central", "m_area_kota", "id_provinsi AS id", "id_kota = '" . $id_kota . "'")->id ?? "";
                    if ($id_provinsi) {
                        $provinsi = _singleData("central", "m_area_provinsi", "nama_provinsi AS `name`", "id_provinsi = '" . $id_provinsi . "'")->name ?? "";
                        return $provinsi;
                    }
                }
            }
        }
        return "";
    }

    public function getKotaKtpAttribute()
    {
        if ($this->id_kelurahan_ktp) {
            $id_kelurahan = $this->id_kelurahan_ktp;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            if ($id_kecamatan) {
                $id_kota = _singleData("central", "m_area_kecamatan", "id_kota AS id", "id_kecamatan = '" . $id_kecamatan . "'")->id ?? "";
                if ($id_kota) {
                    $kota = _singleData("central", "m_area_kota", "nama_kota AS `name`", "id_kota = '" . $id_kota . "'")->name ?? "";
                    return $kota;
                }
            }
        }
        return "";
    }

    public function getKecamatanKtpAttribute()
    {
        if ($this->id_kelurahan_ktp) {
            $id_kelurahan = $this->id_kelurahan_ktp;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            if ($id_kecamatan) {
                $kecamatan = _singleData("central", "m_area_kecamatan", "nama_kecamatan AS `name`", "id_kecamatan = '" . $id_kecamatan . "'")->name ?? "";
                return $kecamatan;
            }
        }
        return "";
    }

    public function getKelurahanKtpAttribute()
    {
        if ($this->id_kelurahan_ktp) {
            $id_kelurahan = $this->id_kelurahan_ktp;
            $kelurahan = _singleData("central", "m_area_kelurahan", "nama_kelurahan AS `name`", "id_kelurahan = '" . $id_kelurahan . "'")->name ?? "";
            return $kelurahan;
        }
        return "";
    }

    public function getProvinsiDomisiliAttribute()
    {
        if ($this->id_kelurahan_domisili) {
            $id_kelurahan = $this->id_kelurahan_domisili;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            if ($id_kecamatan) {
                $id_kota = _singleData("central", "m_area_kecamatan", "id_kota AS id", "id_kecamatan = '" . $id_kecamatan . "'")->id ?? "";
                if ($id_kota) {
                    $id_provinsi = _singleData("central", "m_area_kota", "id_provinsi AS id", "id_kota = '" . $id_kota . "'")->id ?? "";
                    if ($id_provinsi) {
                        $provinsi = _singleData("central", "m_area_provinsi", "nama_provinsi AS `name`", "id_provinsi = '" . $id_provinsi . "'")->name ?? "";
                        return $provinsi;
                    }
                }
            }
        }
        return "";
    }

    public function getKotaDomisiliAttribute()
    {
        if ($this->id_kelurahan_domisili) {
            $id_kelurahan = $this->id_kelurahan_domisili;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            if ($id_kecamatan) {
                $id_kota = _singleData("central", "m_area_kecamatan", "id_kota AS id", "id_kecamatan = '" . $id_kecamatan . "'")->id ?? "";
                if ($id_kota) {
                    $kota = _singleData("central", "m_area_kota", "nama_kota AS `name`", "id_kota = '" . $id_kota . "'")->name ?? "";
                    return $kota;
                }
            }
        }
        return "";
    }

    public function getKecamatanDomisiliAttribute()
    {
        if ($this->id_kelurahan_domisili) {
            $id_kelurahan = $this->id_kelurahan_domisili;
            $id_kecamatan = _singleData("central", "m_area_kelurahan", "id_kecamatan AS id", "id_kelurahan = '" . $id_kelurahan . "'")->id ?? "";
            if ($id_kecamatan) {
                $kecamatan = _singleData("central", "m_area_kecamatan", "nama_kecamatan AS `name`", "id_kecamatan = '" . $id_kecamatan . "'")->name ?? "";
                return $kecamatan;
            }
        }
        return "";
    }

    public function getKelurahanDomisiliAttribute()
    {
        if ($this->id_kelurahan_domisili) {
            $id_kelurahan = $this->id_kelurahan_domisili;
            $kelurahan = _singleData("central", "m_area_kelurahan", "nama_kelurahan AS `name`", "id_kelurahan = '" . $id_kelurahan . "'")->name ?? "";
            return $kelurahan;
        }
        return "";
    }

    public function getBmiAttribute()
    {
        $row = _singleData("central", "pegawai_info", "tinggi,berat", "nip_nik = '" . $this->nip_nik . "'");
        if ($row) {
            //  [berat (kg) / (tinggi (m) *2)]
            $bmi = 0;
            if ($row->tinggi && $row->berat) {
                $bmi = ($row->berat / (($row->tinggi / 100) * 2));
            }
            $lowest = 15;
            $highest = 29;
            $bmi_name = "Tidak Diketahui";
            $bmi_value = $lowest;
            $bmi_label = "default";
            if ($bmi > 0) {
                if ($bmi > $highest) {
                    $bmi = $highest;
                } else if ($bmi < $lowest) {
                    $bmi = $lowest;
                }
                if ($bmi < 17) {
                    $bmi_value = ((($bmi - 15) / 1.9) * 14);
                    $bmi_name = "Kurus\n(BERAT)";
                    $bmi_label = "warning-bold";
                } else if ($bmi >= 17 and $bmi <= 18.4) {
                    $bmi_value = (((($bmi - 17) / 1.4) * 10) + 14);
                    $bmi_name = "Kurus\n(RINGAN)";
                    $bmi_label = "warning";
                } else if ($bmi >= 18.5 and $bmi <= 25) {
                    $bmi_value = (((($bmi - 18.5) / 6.5) * 48) + 24);
                    $bmi_name = "NORMAL";
                    $bmi_label = "success";
                } else if ($bmi >= 25.1 and $bmi <= 27) {
                    $bmi_value = (((($bmi - 25.1) / 1.9) * 14) + 72);
                    $bmi_name = "Gemuk\n(RINGAN)";
                    $bmi_label = "danger";
                } else if ($bmi > 27) {
                    $bmi_value = (((($bmi - 27.1) / 1.9) * 14) + 86);
                    $bmi_name = "Gemuk\n(BERAT)";
                    $bmi_label = "danger-bold";
                }
                $explode = explode(".", $bmi_value);
                if (COUNT($explode) > 1) {
                    $bmi_value = $explode[0] . "." . substr($explode[1], 0, 1);
                }
            }
            $bmi_info =
                [
                    "name" => (string)$bmi_name,
                    "description" => (string)_numdec($bmi, 1),
                    "value" => (float)$bmi_value,
                    "label" => (string)$bmi_label,
                ];
            return $bmi_info;
        }
        return NULL;
    }

    public function getRoleAttribute()
    {
        #==DUMMY==#
        /*
        $row = _singleData("default", "apps_role", "id,`name`,description", "id = 2");
        $data =
            [
                "id" => (int)$row->id,
                "name" => (string)$row->name,
                "description" => (string)$row->description,
            ];
        return json_decode(json_encode($data));
        */
        if ($this->id_penugasan > 0) {
            #==SPECIAL==#
            $row = _singleData("default", "apps_role", "id,`name`,description", "FIND_IN_SET('" . $this->nip_nik  . "',pegawai_ids) AND LENGTH(pegawai_ids) > 0 AND `status` = 1");
            if ($row) {
                $data =
                    [
                        "id" => (int)$row->id,
                        "name" => (string)$row->name,
                        "description" => (string)$row->description,
                    ];
                return json_decode(json_encode($data));
            }
            #==GENERAL==#
            $row = _singleData("default", "apps_role", "id,`name`,description", "id_penugasan = '" . $this->id_penugasan . "' AND id_penugasan > 0 AND `status` = 1");
            if ($row) {
                $data =
                    [
                        "id" => (int)$row->id,
                        "name" => (string)$row->name,
                        "description" => (string)_singleData("central", "m_pegawai_penempatan", "nama_penempatan", "id_penempatan = '" . $this->id_penempatan . "'")->nama_penempatan ?? "",
                    ];
                return json_decode(json_encode($data));
            }
        }
        return NULL;
    }

    public function getRolesAttribute()
    {
        $data = [];
        $role = $this->getRoleAttribute();
        if ($role) {
            $data[] = $role;
            $query = _getData("central", "pegawai_jabatan", "id_jabatan,id_penugasan,id_penempatan", "nip_nik = '" . $this->nip_nik . "' AND status_data = 'AKTIF' AND is_deleted = 0 AND (CURDATE() BETWEEN tanggal_mulai_menjabat AND IFNULL(tanggal_selesai_menjabat,CURDATE()))");
            if (COUNT($query)) {
                foreach ($query as $row) {
                    $role = _singleData("default", "apps_role", "id,`name`,description", "id_penugasan = '" . $row->id_penugasan . "' AND id_penugasan > 0 AND `status` = 1");
                    if ($role) {
                        $data[] =
                            [
                                "id" => (int)$role->id,
                                "name" => (string)$role->name,
                                "description" => (string)_singleData("central", "m_pegawai_penempatan", "nama_penempatan", "id_penempatan = '" . $row->id_penempatan . "'")->nama_penempatan ?? "",
                            ];
                    }
                }
            }
        }
        return $data;
    }

    public function getPrivilegesAttribute()
    {
        $data = [];
        $role = json_decode(json_encode($this->getRoleAttribute()));
        if (isset($role->id)) {
            $conn = DB::connection('default');
            $_apps = $conn->table('apps_privilege AS a')
                ->join('apps_module AS b', 'a.module_id', 'b.id')
                ->select('b.app AS name')
                ->where(['a.role_id' => $role->id, 'a.status' => 1, 'a.is_deleted' => 0, 'b.status' => 1])
                ->groupBy('b.app')
                ->orderBy('b.id', 'asc')
                ->get();
            foreach ($_apps as $app) {
                $pages = [];
                $_pages = $conn->table('apps_privilege AS a')
                    ->join('apps_module AS b', 'a.module_id', 'b.id')
                    ->select('b.page AS name')
                    ->where(['a.role_id' => $role->id, 'a.status' => 1, 'a.is_deleted' => 0, 'b.status' => 1])
                    ->where('b.app', $app->name)
                    ->groupBy('b.page')
                    ->orderBy('b.id', 'asc')
                    ->get();
                foreach ($_pages as $page) {
                    $modules = [];
                    $_modules = $conn->table('apps_privilege AS a')
                        ->join('apps_module AS b', 'a.module_id', 'b.id')
                        ->select('b.ref', 'b.name')
                        ->where(['a.role_id' => $role->id, 'a.status' => 1, 'a.is_deleted' => 0, 'b.status' => 1])
                        ->where('b.app', $app->name)
                        ->where('b.page', $page->name)
                        ->orderBy('b.sort', 'asc')
                        ->get();
                    foreach ($_modules as $module) {
                        $modules[] =
                            [
                                "ref" => $module->ref,
                                "name" => $module->name,
                            ];
                    }
                    $pages[] =
                        [
                            "name" => $page->name,
                            "modules" => $modules,
                        ];
                }
                $data[] =
                    [
                        "name" => $app->name,
                        "pages" => $pages,
                    ];
            }
        }
        return json_decode(json_encode($data));
    }

    public function getIsValidAttribute()
    {
        $privileges = $this->getPrivilegesAttribute();
        return (bool)(COUNT($privileges) > 0);
    }

    protected function casts(): array
    {
        return [
            'tgl_lahir' => 'date:d F Y',
            'tmt_pangkat' => 'date:d F Y',
            'tmt_eselon' => 'date:d F Y',
            'tmt_cpns' => 'date:d F Y',
            'bmi' => 'array',
            'roles' => 'array',
            'privileges' => 'array',
            'is_valid' => 'boolean',
        ];
    }
}
