<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\Notification;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use DataTables;

class DashboardController extends Controller
{


    public function index()
    {
        $total = _singleData("central", "pegawai", "COUNT(DISTINCT nip_nik) AS total", "id_status = 1 AND is_deleted = 0")->total ?? 0;
        $query = DB::connection("central")->table("pegawai AS p")
            ->join(config('database.connections.damkarone.database') . ".user_pegawai AS up", "p.nrk_id_pjlp", "up.pegawai_id")
            ->selectRaw("p.nip_nik")
            ->whereRaw("p.id_status = 1 AND p.is_deleted = 0");
        $active = $query->count();
        $percent = 0;
        if ($total > 0 and $active > 0) {
            $percent = (($active / $total) * 100);
        }
        $label = "default";
        if ($percent >= 80) {
            $label = "success";
        } else if ($percent >= 70) {
            $label = "info";
        } else if ($percent >= 50) {
            $label = "warning";
        } else if ($percent > 0) {
            $label = "danger";
        }
        $data["label"] = $label;
        $data["value"] = $percent;
        return view('dashboard.view', $data);
    }

    public function pegawai_data(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::connection("central")->table("pegawai AS p")
                ->leftJoin(config('database.connections.default.database') . ".user_pegawai AS up", "p.nrk_id_pjlp", "up.pegawai_id")
                ->leftJoin("m_pegawai_jenis AS pj", "p.id_jenis_pegawai", "pj.id_jenis_pegawai")
                ->leftJoin("m_pegawai_unit_kerja AS uk", "p.id_unit_kerja", "uk.id_unit_kerja")
                ->leftJoin("m_pegawai_unit_kerja_sub AS uks", "p.id_sub_unit_kerja", "uks.id_sub_unit_kerja")
                ->selectRaw("p.nrk_id_pjlp AS nrk,p.gelar_depan,p.gelar_belakang,p.nama_pegawai AS nama")
                ->selectRaw("pj.nama_jenis_pegawai AS jenis,uk.nama_unit_kerja AS unit_kerja,uks.nama_sub_unit_kerja AS sub_unit_kerja")
                ->selectRaw("IF(up.id > 0,1,0) AS `status`")
                ->when($request->string('search'), function (Builder $query, string $search) {
                    if ($search) {
                        $query->whereAny([
                            'p.nama_pegawai',
                            'p.nrk_id_pjlp',
                        ], 'LIKE', "%" . $search . "%");
                    }
                })
                ->when($request->integer('jenis'), function (Builder $query, int $search) {
                    if ($search) {
                        $query->where('p.id_jenis_pegawai', $search);
                    }
                })
                ->when($request->integer('unit_kerja'), function (Builder $query, int $search) {
                    if ($search) {
                        $query->where('p.id_unit_kerja', $search);
                    }
                })
                ->when($request->integer('sub_unit_kerja'), function (Builder $query, int $search) {
                    if ($search) {
                        $query->where('p.id_sub_unit_kerja', $search);
                    }
                })
                ->whereRaw("p.id_status = 1 AND p.is_deleted = 0");
            if (($request->input('status') ?? -1) >= 0) {
                $query->whereRaw("IF(up.id > 0,1,0) = '" . $request->input('status') . "'");
            }
            $data = $query->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama', function ($row) {
                    $html = ($row->gelar_depan ? $row->gelar_depan . " " : "") . $row->nama . ($row->gelar_belakang ? " " . $row->gelar_belakang : "");
                    return $html;
                })
                ->editColumn('status', function ($row) {
                    $html = '<span class="badge badge-' . ($row->status == 1 ? "success" : "danger") . '">' . ($row->status == 1 ? "Sudah Login" : "Belum Login") . '</span>';
                    return $html;
                })
                ->removeColumn(['gelar_depan', 'gelar_belakang'])
                ->rawColumns(['nama', 'status'])
                ->toJson();
        }
    }

    public function pegawai_export(Request $request)
    {
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename = DATA_LOGIN_PEGAWAI.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        $query = DB::connection("central")->table("pegawai AS p")
            ->leftJoin(config('database.connections.default.database') . ".user_pegawai AS up", "p.nrk_id_pjlp", "up.pegawai_id")
            ->leftJoin("m_pegawai_jenis AS pj", "p.id_jenis_pegawai", "pj.id_jenis_pegawai")
            ->leftJoin("m_pegawai_unit_kerja AS uk", "p.id_unit_kerja", "uk.id_unit_kerja")
            ->leftJoin("m_pegawai_unit_kerja_sub AS uks", "p.id_sub_unit_kerja", "uks.id_sub_unit_kerja")
            ->selectRaw("p.nrk_id_pjlp AS nrk,p.gelar_depan,p.gelar_belakang,p.nama_pegawai AS nama")
            ->selectRaw("pj.nama_jenis_pegawai AS jenis,uk.nama_unit_kerja AS unit_kerja,uks.nama_sub_unit_kerja AS sub_unit_kerja")
            ->selectRaw("IF(up.id > 0,1,0) AS `status`")
            ->when($request->string('search'), function (Builder $query, string $search) {
                if ($search) {
                    $query->whereAny([
                        'p.nama_pegawai',
                        'p.nrk_id_pjlp',
                    ], 'LIKE', "%" . $search . "%");
                }
            })
            ->when($request->integer('jenis'), function (Builder $query, int $search) {
                if ($search) {
                    $query->where('p.id_jenis_pegawai', $search);
                }
            })
            ->when($request->integer('unit_kerja'), function (Builder $query, int $search) {
                if ($search) {
                    $query->where('p.id_unit_kerja', $search);
                }
            })
            ->when($request->integer('sub_unit_kerja'), function (Builder $query, int $search) {
                if ($search) {
                    $query->where('p.id_sub_unit_kerja', $search);
                }
            })
            ->whereRaw("p.id_status = 1 AND p.is_deleted = 0");
        if (($request->input('status') ?? -1) >= 0) {
            $query->whereRaw("IF(up.id > 0,1,0) = '" . $request->input('status') . "'");
        }

        $table = '<table>';
        if ($query->count() > 0) {
            $no = 1;
            $table .= '<thead>';
            $table .= '<tr>';
            $table .= '<th style="text-align:left;border:1px black solid;">NO</th>';
            $table .= '<th style="text-align:left;border:1px black solid;">NRK/ID PJLP</th>';
            $table .= '<th style="text-align:left;border:1px black solid;">NAMA</th>';
            $table .= '<th style="text-align:left;border:1px black solid;">JENIS PEGAWAI</th>';
            $table .= '<th style="text-align:left;border:1px black solid;">UNIT KERJA</th>';
            $table .= '<th style="text-align:left;border:1px black solid;">SUB UNIT KERJA</th>';
            $table .= '<th style="text-align:left;border:1px black solid;">STATUS</th>';
            $table .= '</tr>';
            $table .= '</thead>';
            $table .= '<tbody>';
            foreach ($query->get() as $row) {
                $table .= '<tr>';
                $table .= '<td style="text-align:left;border:1px black solid;">' . $no . '</td>';
                $table .= '<td style="text-align:left;border:1px black solid;">' . $row->nrk . '</td>';
                $table .= '<td style="text-align:left;border:1px black solid;">' . ($row->gelar_depan ? $row->gelar_depan . " " : "") . $row->nama . ($row->gelar_belakang ? " " . $row->gelar_belakang : "") . '</td>';
                $table .= '<td style="text-align:left;border:1px black solid;">' . $row->jenis . '</td>';
                $table .= '<td style="text-align:left;border:1px black solid;">' . $row->unit_kerja . '</td>';
                $table .= '<td style="text-align:left;border:1px black solid;">' . $row->sub_unit_kerja . '</td>';
                $table .= '<td style="text-align:left;border:1px black solid;">' . ($row->status == 1 ? "Sudah Login" : "Belum Login") . '</td>';
                $table .= '</tr>';
                $no++;
            }
            $table .= '</tbody>';
        } else {
            $table .= '<tr><td style="text-align:center;border:2px black solid;" colspan="7">No data to display!</td></tr>';
        }

        $table .= '</table>';
        echo $table;
    }

    public function notif_data(Request $request)
    {
        if ($request->ajax()) {
            $where = "DATE(IFNULL(a.updated_at,a.created_at)) = CURDATE()";
            $query = DB::connection("default")->table("fcm_token AS a")
                ->join(config('database.connections.central.database') . ".pegawai AS b", "a.ref_id", "b.nrk_id_pjlp")
                ->selectRaw("a.id,a.device_name AS `device`")
                ->selectRaw("b.nrk_id_pjlp,b.nama_pegawai")
                ->when($request->input('search'), function (Builder $query, string $search) {
                    if ($search) {
                        $query->whereAny([
                            'a.device_name',
                            'b.nama_pegawai',
                            'b.nrk_id_pjlp',
                        ], 'LIKE', "%" . $search . "%");
                    }
                })
                ->whereRaw($where);
            $data = $query->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    $html = '';
                    if (config('app.user_access.delete', 0) == 1) {
                        $html = '<div class="form-check form-check-sm form-check-custom form-check-solid checkbox-table"><input type="checkbox" class="form-check-input" value="' . $row->id . '" /></div>';
                    }
                    return $html;
                })
                ->addColumn('user', function ($row) {
                    $html = $row->nama_pegawai;
                    $html .= "<br/>NRK: " . $row->nrk_id_pjlp;
                    return $html;
                })
                ->removeColumn(['nrk_id_pjlp'])
                ->rawColumns(['checkbox', 'user'])
                ->toJson();
        }
    }

    public function notif_send(Request $request)
    {
        try {
            if (!$request->id) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.no_process'),
                ]);
            }
            $query = DB::table('fcm_token')->select('ref_id', 'token')->whereIn('id', explode(",", $request->id));
            $rows = $query->get();
            if (!$rows->count()) {
                abort(400);
            }
            foreach ($rows as $row) {
                #==KEJADIAN==#
                /*
                */
                $ref_data = ["ref" => "kejadian", "ref_id" => "CC1_0000018772"];
                $notif = new Notification();
                $notif->type = "cc";
                $notif->platform = "damkarone";
                $notif->title = "Kejadian Kebakaran Bangunan Umum dan Perdagangan";
                $notif->message = "Jl. Pergudangan Rawa Melati  Titik kenal Masjid Istiqomah Kel. Tegal Alur Kec. Kalideres";
                $notif->ref = "pegawai";
                $notif->ref_id = $row->ref_id;
                $notif->ref_action = "data";
                $notif->ref_data = json_encode($ref_data);
                $notif->token = $row->token;
                $notif->save();

                #==NEWS==#
                /*
                $ref_data = ["ref" => "news", "ref_id" => 1];
                $notif = new Notification();
                $notif->type = "general";
                $notif->platform = "damkarone";
                $notif->title = "Info Update";
                $notif->message = "Waspada Potensi Bahaya di Musim Hujan";
                $notif->ref = "pegawai";
                $notif->ref_id = $row->ref_id;
                $notif->ref_action = "data";
                $notif->ref_data = json_encode($ref_data);
                $notif->token = $row->token;
                $notif->save();
                */

                app(\App\Classes\FcmController::class)->send($notif->id);
            }
            return response()->json([
                'status' => TRUE,
                'message' => __('response.data_sent'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => FALSE,
                'message' => $e->getMessage()
            ]);
        }
    }
}
