<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Sso\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Validation\Rule;
use DataTables;

class ClientController extends Controller
{


    public function data(Request $request)
    {
        if ($request->ajax()) {
            $where = "a.is_deleted = 0";
            $data = DB::connection("central")->table("sso_client AS a")
                ->leftJoin("m_pegawai_penugasan AS b", function (JoinClause $join) use ($request) {
                    $join->on(DB::raw("FIND_IN_SET(b.id_penugasan, a.penugasan_ids)"), ">", DB::raw("0"))
                        ->whereRaw("b.is_deleted = 0");
                })
                ->selectRaw("a.id,a.`name`,a.url_web,a.api,a.web,a.`status`")
                ->selectRaw("IF(a.penugasan_ids != -1, GROUP_CONCAT(DISTINCT b.nama_penugasan ORDER BY b.nama_penugasan ASC SEPARATOR ', '), 'All') AS penugasan")
                ->whereRaw($where)
                ->when($request->input('search'), function (Builder $query, string $search) {
                    if ($search) {
                        $query->whereAny([
                            'name',
                        ], 'LIKE', "%" . $search . "%");
                    }
                })
                ->when($request->input('status'), function (Builder $query, int $search) {
                    if ($search) {
                        $query->where('status', $search);
                    }
                })
                ->groupBy("a.id")
                ->orderBy("a.sort", "asc")
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    $html = '';
                    if (config('app.user_access.delete', 0) == 1) {
                        $html = '<div class="form-check form-check-sm form-check-custom form-check-solid checkbox-table"><input type="checkbox" class="form-check-input" value="' . $row->id . '" /></div>';
                    }
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $html = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'manage/client/detail\',\'id=' . $row->id . '\')" title="Detail"><i class="fa fa-th-list"></i></a>';
                    if (config('app.user_access.update', 0) == 1) {
                        $html .= ' <a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'manage/client/form\',\'id=' . $row->id . '\')" title="Update"><i class="fa fa-edit"></i></a>';
                    }
                    return $html;
                })
                ->editColumn('name', function ($row) {
                    $html = $row->name;
                    $html .= '<br/><a href="' . $row->url_web . '" target="_blank">Go to Website <i class="fa fa-external-link-alt"></i></a>';
                    return $html;
                })
                ->editColumn('api', function ($row) {
                    $html = "";
                    $html = '<div class="form-check form-switch form-check-custom form-check-solid">
                                    <input type="checkbox" class="form-check-input status-' . $row->id . '" value="' . $row->api . '" ' . ($row->api == 1 ? 'checked' : '') . ' onchange="changeStatus(this,' . $row->id . ',\'manage/client/status\',\'api\')" />
                                </div>';
                    return $html;
                })
                ->editColumn('web', function ($row) {
                    $html = "";
                    $html = '<div class="form-check form-switch form-check-custom form-check-solid">
                                    <input type="checkbox" class="form-check-input status-' . $row->id . '" value="' . $row->web . '" ' . ($row->web == 1 ? 'checked' : '') . ' onchange="changeStatus(this,' . $row->id . ',\'manage/client/status\',\'web\')" />
                                </div>';
                    return $html;
                })
                ->editColumn('status', function ($row) {
                    $html = "";
                    $html = '<div class="form-check form-switch form-check-custom form-check-solid">
                                    <input type="checkbox" class="form-check-input status-' . $row->id . '" value="' . $row->status . '" ' . ($row->status == 1 ? 'checked' : '') . ' onchange="changeStatus(this,' . $row->id . ',\'manage/client/status\')" />
                                </div>';
                    return $html;
                })
                ->removeColumn(['url_web'])
                ->rawColumns(['checkbox', 'action', 'name', 'api', 'web', 'status'])
                ->toJson();
        }
    }

    public function form(Request $request)
    {
        if ($request->id) {
            $row = DB::connection("central")->table('sso_client')->find($request->id);
            if (!$row) {
                abort(400);
            }
            $data["title"] = "Ubah Client";
            $data["row"] = $row;
            $data["image"] = _diskPathUrl('central', $row->image, config('app.placeholder.default'));
        } else {
            $data["title"] = "Tambah Client";
            $data["image"] = config('app.placeholder.default');
        }
        return view('manage.client.form', $data);
    }

    public function save(Request $request)
    {
        try {
            $request->validate([
                'image' => (!$request->id ? 'required|' : '') . 'image|mimes:jpeg,png,jpg|max:2048',
                'name' => 'required|string',
                'url_web' => 'required|string',
                'url_auth' => 'required|string',
                'penugasan_ids' => 'array',
                'api' => [Rule::in([0, 1])],
                'web' => [Rule::in([0, 1])],
                'status' => [Rule::in([0, 1])],
            ]);

            if ($request->image) {
                $path = $request->image->storeAs(
                    '',
                    'client_' . _uuid(),
                    'central'
                );
                $data['image'] = $path;
                if ($request->image_old) {
                    _removeDiskPathUrl('uploads', $request->image_old);
                }
            }
            $user_id = $request->user()->id;
            $data['name'] = $request->string('name');
            $data['url_web'] = $request->string('url_web');
            $data['url_auth'] = $request->string('url_auth');
            $data['penugasan_ids'] = ($request->penugasan_ids ? implode(",", array_map("intval", $request->penugasan_ids)) : -1);
            $data['api'] = $request->api ?? 0;
            $data['web'] = $request->web ?? 0;
            $data['status'] = $request->status ?? 0;
            if ($request->id) {
                $data['updated_from'] = 'Back Office';
                $data['updated_by'] = $user_id;
                _updateData("central", "sso_client", $data, "id = '" . $request->integer('id') . "'");
            } else {
                $name = preg_replace('/[^A-Za-z0-9\-]/', '', strtolower(_escape($request->string('name'))));
                $data['client_id'] = substr(str_replace("-", "", $name), 0, 6) . "-" . Str::random(15);
                $data['client_secret'] = Str::random(64);
                $data['created_from'] = 'Back Office';
                $data['created_by'] = $user_id;
                _insertData("central", "sso_client", $data);
            }
            return response()->json([
                'status' => TRUE,
                'message' => __(($request->id ? 'response.data_updated' : 'response.data_added')),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => FALSE,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => FALSE,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function del(Request $request)
    {
        try {
            if (!$request->id) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.no_process'),
                ]);
            }

            $where = "id IN(" . $request->string('id') . ") AND is_deleted = 0";
            $rows = _getData("central", "sso_client", "id,image", $where);
            if (!COUNT($rows)) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.data_invalid'),
                ]);
            }
            foreach ($rows as $row) {
                _removeDiskPathUrl('central', $row->image);
            }

            $data['is_deleted'] = 1;
            $data['deleted_at'] = now();
            $data['deleted_from'] = "Back Office";
            $data['deleted_by'] = $request->user()->id;
            _updateData("central", "sso_client", $data, $where);
            return response()->json([
                'status' => TRUE,
                'message' => __('response.data_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => FALSE,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function status(Request $request)
    {
        try {
            if (!$request->id) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.no_process'),
                ]);
            }

            $where = "id = '" . $request->integer('id') . "' AND is_deleted = 0";
            $row = _singleData("central", "sso_client", "id", $where);
            if (!$row) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.data_invalid'),
                ]);
            }

            $data[$request->field] = (int)($request->status ?? 0);
            $data['updated_at'] = now();
            $data['updated_from'] = "Back Office";
            $data['updated_by'] = $request->user()->id;
            _updateData("central", "sso_client", $data, $where);
            return response()->json([
                'status' => TRUE,
                'message' => __('response.data_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => FALSE,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function detail(Request $request)
    {
        if (!$request->id) {
            abort(400);
        }
        $row = Client::find($request->id);
        if (!$row) {
            abort(400);
        }
        $data["title"] = "Detail Client";
        $data["row"] = $row;
        return view('manage.client.detail', $data);
    }
}
