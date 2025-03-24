<?php

namespace App\Models\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EsatgasSosialisasi extends Model
{

    public $timestamps = false;

    protected $connection = 'esatgas';
    protected $table = 'sosialisasi';
    protected $primaryKey = 'no_sosialisasi';

    protected $fillable = [
        'no_sosialisasi',
        'kd_sosialisasi',
        'no_pegawai',
        'materi',
        'penerima_layanan',
        'nama_sosialisasi',
        'alamat_sosialisasi',
        'tanggal',
        'jam_awal',
        'jam_akhir',
        'sumber_dana',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'jumlah_rt',
        'jumlah_peserta',
        'tautan_foto',
        'tautan_foto2',
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
        'no_sosialisasi',
        'kd_sosialisasi',
        'no_pegawai',
        'nama_sosialisasi',
        'alamat_sosialisasi',
        'tanggal',
        'jam_awal',
        'jam_akhir',
        'no_kota',
        'nama_kota',
        'no_kecamatan',
        'nama_kecamatan',
        'no_kelurahan',
        'nama_kelurahan',
        'tautan_foto',
        'tautan_foto2',
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
        'details',
    ];

    protected $appends =
    [
        'location',
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
        $path = config('filesystems.assets.esatgas') . "sosialisasi/";
        $query->selectRaw("sosialisasi.no_sosialisasi AS id,sosialisasi.kd_sosialisasi AS `code`,sosialisasi.keterangan AS note");
        $query->selectRaw("CONCAT('" . $path . "', sosialisasi.tautan_foto) AS image");
        $query->selectRaw("CONCAT(DATE_FORMAT(sosialisasi.tanggal, '%d %M %Y'),' ', LEFT(sosialisasi.jam_awal,5), ' - ', LEFT(sosialisasi.jam_akhir,5)) AS `period`");
        $query->selectRaw("CONCAT(sosialisasi.tanggal, ' ', sosialisasi.jam_awal) AS `start`,CONCAT(sosialisasi.tanggal, ' ', sosialisasi.jam_akhir) AS `end`");
        $query->selectRaw("CONCAT(FLOOR(TIME_TO_SEC(TIMEDIFF(sosialisasi.jam_akhir,sosialisasi.jam_awal)) / 60), ' menit') AS `duration`");
        $query->selectRaw("sosialisasi.*");
        $query->selectRaw("GROUP_CONCAT(CONCAT(sosialisasi_detail.no_rw,'||',sosialisasi_detail.no_rt) ORDER BY sosialisasi_detail.no_rw,sosialisasi_detail.no_rt ASC SEPARATOR '__') AS details");
        $query->selectRaw("'" . $user->nrk . "' AS user_login");
        if (in_array($user->role_id, [1, 2]) or $user->level->id == 16) {
            $query->selectRaw("'1' AS is_verificator");
        }
        if (!in_array($user->role_id, [1, 2, 3]) && !$user->bypass_area) {
            if (in_array($user->level->id, [99])) {
                $query->whereRaw("sosialisasi.no_pegawai = '" . $user->nrk . "'");
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
        $query->leftJoin("sosialisasi_detail", "sosialisasi.no_sosialisasi", "sosialisasi_detail.sosialisasi_no");
        $query->groupBy("sosialisasi.no_sosialisasi");
        return $query;
    }

    public function scopeFilter(Builder $query, Request $request)
    {
        $query->when($request->string('search'), function (Builder $query, string $value) {
            if ($value) {
                $query->whereAny([
                    'sosialisasi.kd_sosialisasi',
                    'sosialisasi.materi',
                    'sosialisasi.keterangan',
                ], 'LIKE', "%" . $value . "%");
            }
        })
            ->when($request->string('period'), function (Builder $query, string $value) use ($request) {
                if ($value) {
                    if ($value == 'today') {
                        $query->whereRaw('DATE(sosialisasi.created_at) = CURDATE()');
                    } else if ($value == 'yesterday') {
                        $query->whereRaw('DATE(sosialisasi.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)');
                    } else if ($value == 'lastweek') {
                        $query->whereRaw('DATE(sosialisasi.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)');
                    } else if ($value == 'lastmonth') {
                        $query->whereRaw('DATE(sosialisasi.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)');
                    } else if ($value == 'daterange' && $request->string('start_date') and $request->string('end_date')) {
                        $query->whereRaw('DATE(sosialisasi.created_at) BETWEEN ? AND ?', [$request->string('start_date'), $request->string('end_date')]);
                    }
                }
            })
            ->when($request->string('penerima_layanan'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('sosialisasi.penerima_layanan', explode(',', $value));
                }
            })
            ->when($request->string('sumber_dana'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('sosialisasi.sumber_dana', explode(',', $value));
                }
            })
            ->when($request->string('status'), function (Builder $query, string $value) {
                if ($value) {
                    $query->whereIn('sosialisasi.status_validasi', explode(',', $value));
                }
            })
            ->when($request->string('sort', 'latest'), function (Builder $query, string $value) {
                if ($value == 'oldest') {
                    $query->orderByRaw('sosialisasi.created_at ASC');
                } else {
                    $query->orderByRaw('sosialisasi.created_at DESC');
                }
            });
        return $query;
    }

    public function getLocationAttribute()
    {
        $address = _strip($this->alamat_sosialisasi);
        $address .= ($address ? "\n" : "") . $this->nama_kelurahan . ", " . $this->nama_kecamatan . " - " . $this->nama_kota;
        $rws = [];
        if ($this->details) {
            $items = [];
            foreach (explode("__", $this->details) as $rw) {
                $exp = explode("||", $rw);
                $items[$exp[0]][] = $exp[1];
            }
            if (COUNT($items)) {
                foreach ($items as $key => $value) {
                    $rws[] = [$key => implode(", ", $value)];
                }
            }
        }
        $data =
            [
                "nama" => (string)$this->nama_sosialisasi,
                "alamat" => (string)$address,
                "rws" => $rws,
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
            'duration' => 'string',
            'location' => 'array',
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
