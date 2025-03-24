<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bangunan extends Model
{

    protected $connection = 'central';
    protected $table = 'm_pegawai_lokasi';

    protected $hidden = [
        'id_lokasi',
        'nama_lokasi',
        'no_telepon',
        'alamat',
        'id_pos',
        'id_sektor',
        'id_wilayah',
        'id_kelurahan',
        'id_kategori_bangunan',
        'id_status_bangunan',
        'id_status_tanah',
        'kode_panggil_bangunan',
        'status',
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

    protected $appends = ['phone', 'category'];

    public function scopeApi(Builder $query, Request $request)
    {
        $latitude = $request->latitude ?? 0;
        $longitude = $request->longitude ?? 0;
        $query->selectRaw("id_lokasi AS id,TRIM(nama_lokasi) AS `name`,IF(LENGTH(no_telepon) > 3, no_telepon, '') AS phone,TRIM(alamat) AS address,latitude,longitude,id_kategori_bangunan")
            ->whereRaw("status = 1 AND is_deleted = 0")
            ->when($request->string('search'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereAny([
                        'nama_lokasi',
                        'alamat',
                    ], 'LIKE', "%" . $value . "%");
                }
            })
            ->when($request->string('id'), function (Builder $query, string $value) {
                if ($value) {
                    $query->where('id_lokasi', $value);
                }
            })
            ->when($request->string('kategori_bangunan_ids'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('id_kategori_bangunan', explode(',', $value));
                }
            })
            ->when($request->string('kategori_bangunan_id'), function (Builder $query, string $value) {
                if ($value) {
                    $query->where('id_kategori_bangunan', $value);
                }
            })
            ->when($request->string('wilayah_ids'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('id_wilayah', explode(',', $value));
                }
            })
            ->when($request->string('wilayah_id'), function (Builder $query, string $value) {
                if ($value) {
                    $query->where('id_wilayah', $value);
                }
            })
            ->when($request->string('sektor_ids'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('id_sektor', explode(',', $value));
                }
            })
            ->when($request->string('sektor_id'), function (Builder $query, string $value) {
                if ($value) {
                    $query->where('id_sektor', $value);
                }
            })
            ->when($request->string('pos_ids'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('id_pos', explode(',', $value));
                }
            })
            ->when($request->string('pos_id'), function (Builder $query, string $value) {
                if ($value) {
                    $query->where('id_pos', $value);
                }
            });
        if ($latitude != 0 && $longitude != 0) {
            $query->selectRaw("LAT_LNG_DISTANCE(latitude,longitude,'" . $latitude . "','" . $longitude . "') AS distance");
            $query->orderBy('distance', 'asc');
        } else {
            $query->selectRaw("0 AS distance");
            $query->orderBy('name', 'asc');
        }
        return $query;
    }

    public function getPhoneAttribute()
    {
        return _convertPhone($this->attributes['phone']);
    }

    public function getCategoryAttribute()
    {
        $path = config('filesystems.assets.images') . 'marker/';
        return DB::connection('central')->table('m_bangunan_kategori')->selectRaw("id_kategori_bangunan AS id,nama_kategori_bangunan AS name,label,CONCAT('" . $path . "',marker) AS marker")->where('id_kategori_bangunan', $this->id_kategori_bangunan)->first();
    }

    public function getDistanceAttribute()
    {
        return ($this->attributes['distance'] ? _numdec($this->attributes['distance']) . ' km' : '');
    }

    protected function casts(): array
    {
        return [
            'phone' => 'string',
            'latitude' => 'double',
            'longitude' => 'double',
            'distance' => 'double',
            'category' => 'array',
        ];
    }
}
