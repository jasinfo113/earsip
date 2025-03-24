<?php

namespace App\Models\Api;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Api\V1\Pegawai;
use App\Models\Apps\AppsRole;
use Illuminate\Support\Facades\DB;

class UserPegawai extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = 'user_pegawai';
    public $timestamps = false;

    protected $fillable = [
        'pegawai_id',
        'role_id',
        'token',
    ];

    protected $hidden = [
        'id',
        'pegawai_id',
        'role_id',
        'token',
        'is_valid',
    ];

    protected $appends = ['data', 'role', 'is_valid'];

    public function getDataAttribute()
    {
        $pegawai = Pegawai::firstWhere('nrk_id_pjlp', $this->pegawai_id);
        if ($pegawai && $pegawai->is_valid) {
            return $pegawai->makeVisible('roles');
        }
        return NULL;
    }

    public function getRoleAttribute()
    {
        $pegawai = $this->data ?? NULL;
        if ($pegawai) {
            $role = AppsRole::active()->find($this->role_id);
            if ($role) {
                $id_penempatan = $pegawai->id_penempatan;
                #==DUMMY==#
                /*
                $row = DB::connection("central")->table("m_pegawai_penempatan AS a")
                    ->join("m_pegawai_lokasi AS b", "a.id_lokasi", "b.id_lokasi")
                    ->selectRaw("a.id_penempatan AS id, a.nama_penempatan AS `name`")
                    ->selectRaw("b.id_wilayah,b.id_sektor,b.id_pos")
                    ->whereRaw("a.id_penempatan = '" . $id_penempatan . "' AND a.is_deleted = 0 AND b.is_deleted = 0")
                    ->first();
                if (isset($row->id)) {
                    $penempatan =
                        [
                            "id" => (int)$row->id,
                            "name" => (string)$row->name,
                            "id_wilayah" => (string)$row->id_wilayah,
                            "id_sektor" => (string)$row->id_sektor,
                            "id_pos" => (string)$row->id_pos,
                        ];
                    $data =
                        [
                            "id" => (int)$role->id,
                            "name" => (string)$role->name,
                            "description" => (string)$role->description,
                            "jabatan" => _singleData("central", "m_pegawai_jabatan", "id_jabatan AS id, nama_jabatan AS `name`", "id_jabatan = '" . $role->id_jabatan . "'"),
                            "penugasan" => _singleData("central", "m_pegawai_penugasan", "id_penugasan AS id, nama_penugasan AS `name`", "id_penugasan = '" . $role->id_penugasan . "'"),
                            "penempatan" => $penempatan,
                            "bypass_area" => (bool)$role->bypass_area,
                            "privileges" => (array)$role->privileges,
                        ];
                    return json_decode(json_encode($data));
                }
                */

                #==SPECIAL==#
                $special = _singleData("default", "apps_role", "id", "id = '" . $role->id  . "' AND FIND_IN_SET('" . $pegawai->nip_nik . "',pegawai_ids) AND LENGTH(pegawai_ids) > 0 AND `status` = 1");
                if (!$special) {
                    #==GENERAL==#
                    $general = _singleData("default", "apps_role", "id", "id = '" . $role->id  . "' AND id_penugasan = '" . $pegawai->id_penugasan . "' AND id_penugasan > 0 AND `status` = 1");
                    if (!$general) {
                        #==JABATAN==#
                        $jabatan = _singleData("central", "pegawai_jabatan", "id_jabatan_pegawai,id_penempatan", "nip_nik = '" . $pegawai->nip_nik . "' AND id_penugasan = '" . $role->id_penugasan . "' AND status_data = 'AKTIF' AND is_deleted = 0 AND (CURDATE() BETWEEN tanggal_mulai_menjabat AND IFNULL(tanggal_selesai_menjabat,CURDATE()))");
                        if (!$jabatan) {
                            return NULL;
                        }
                        $id_penempatan = $jabatan->id_penempatan;
                    }
                }
                $row = DB::connection("central")->table("m_pegawai_penempatan AS a")
                    ->join("m_pegawai_lokasi AS b", "a.id_lokasi", "b.id_lokasi")
                    ->selectRaw("a.id_penempatan AS id, a.nama_penempatan AS `name`")
                    ->selectRaw("b.id_wilayah,b.id_sektor,b.id_pos")
                    ->whereRaw("a.id_penempatan = '" . $id_penempatan . "' AND a.is_deleted = 0 AND b.is_deleted = 0")
                    ->first();
                if (isset($row->id)) {
                    $penempatan =
                        [
                            "id" => (int)$row->id,
                            "name" => (string)$row->name,
                            "id_wilayah" => (string)$row->id_wilayah,
                            "id_sektor" => (string)$row->id_sektor,
                            "id_pos" => (string)$row->id_pos,
                        ];
                    $data =
                        [
                            "id" => (int)$role->id,
                            "name" => (string)$role->name,
                            "description" => (string)$role->description,
                            "jabatan" => _singleData("central", "m_pegawai_jabatan", "id_jabatan AS id, nama_jabatan AS `name`", "id_jabatan = '" . $role->id_jabatan . "'"),
                            "penugasan" => _singleData("central", "m_pegawai_penugasan", "id_penugasan AS id, nama_penugasan AS `name`", "id_penugasan = '" . $role->id_penugasan . "'"),
                            "penempatan" => $penempatan,
                            "bypass_area" => (bool)$role->bypass_area,
                            "privileges" => (array)$role->privileges,
                        ];
                    return json_decode(json_encode($data));
                }
            }
        }
        return NULL;
    }

    public function getIsValidAttribute()
    {
        $pegawai = $this->getDataAttribute();
        $role = $this->getRoleAttribute();
        return (bool)($pegawai && $role && isset($role->privileges) && is_array($role->privileges) && COUNT($role->privileges));
    }
}
