<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\location;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('master.location.view');
    }

    public function location_data(Request $request)
    {
        if ($request->ajax()) {
            $where = "is_deleted = 0";
            $query = DB::connection("default")->table("m_location")->whereRaw($where)
                ->when($request->input('search'), function (Builder $query, string $search) {
                    if ($search) {
                        $query->whereAny([
                            'name',
                            'description',
                            'status',
                        ], 'LIKE', "%" . $search . "%");
                    }
                });
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
                ->addColumn('action', function ($row) {
                    $html = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'master/location/detail\',\'id=' . $row->id . '\')" title="Detail"><i class="fa fa-th-list"></i></a>';
                    if (config('app.user_access.update', 0) == 1) {
                        $html .= ' <a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'master/location/form\',\'id=' . $row->id . '\')" title="Update"><i class="fa fa-edit"></i></a>';
                    }
                    return $html;
                })
                ->editColumn('status', function ($row) {
                    $html = '<span class="badge badge-' . ($row->status == 1 ? "success" : "danger") . '">' . ($row->status == 1 ? "Aktif" : "Tidak Aktif") . '</span>';
                    return $html;
                })

                ->rawColumns(['checkbox', 'action', 'status'])
                ->toJson();
        }
    }

    public function form(Request $request)
    {
        if ($request->id) {
            $row = DB::table('m_location')->find($request->id);
            if (!$row) {
                abort(400);
            }
            $data["title"] = "Ubah Lokasi";
            $data["row"] = $row;
            $data["status"] = collect([
                ["id" => 1, "name" => "Active"],
                ["id" => 2, "name" => "Non Active"]
            ]);
        } else {
            $data["title"] = "Tambah Lokasi";
        }
        return view('master.location.form', $data);
    }

    public function save(Request $request)
    {
        //dd($request->all());
        try {
            if ($request->has('id')) {
                $location = location::findOrFail($request->id);
                $request->validate([
                    'location' => 'required|string',
                    'label' => 'required|string',
                    'keterangan' => 'required|string',
                    'status_id' => 'required',
                ]);

                $location->name = _escape($request->string('location'));
                $location->description = _escape($request->string('keterangan'));
                $location->label = _escape($request->string('label'));
                $location->status = _escape($request->string('status_id'));
                $location->updated_at = now();
                $location->updated_from = 'Back Office';
                $location->updated_by = Auth::user()->id;
                $location->save();
            } else {
                $request->validate([
                    'location' => 'required|string',
                    'label' => 'required|string',
                    'keterangan' => 'required|string',

                ]);
                $data['name'] = _escape($request->string('location'));
                $data['label'] = _escape($request->string('label'));
                $data['description'] = _escape($request->string('keterangan'));
                $data['sort'] = location::max('sort');
                $data['status'] = 1;
                $data['created_from'] = 'Back Office';
                $data['created_by'] = Auth::user()->id;
                location::create($data);
            }
            return response()->json([
                'status' => TRUE,
                'message' => __(($request->id ? 'Data Berhasil Diubah' : 'Data Berhasil Ditambahkan')),
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
                    'message' => __('no_process'),
                ]);
            }
            $query = location::whereIn('id', explode(",", $request->id));
            $rows = $query->get();
            if (!$rows->count()) {
                abort(400);
            }
            $user_id = Auth::user()->id;
            foreach ($rows as $row) {
                if ($row->id == $user_id) {
                    return response()->json([
                        'status' => FALSE,
                        'message' => __('failed_request'),
                    ]);
                }
            }
            $query->update([
                'is_deleted' => 1,
                'deleted_at' => now(),
                'deleted_from' => 'Back Office',
                'deleted_by' => $user_id,
            ]);
            return response()->json([
                'status' => TRUE,
                'message' => __('Data Berhasil Dihapus'),
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
        $row = location::find($request->id);
        if (!$row) {
            abort(400);
        }

        $data["title"] = "Detail Location";
        $data["row"] = $row;
        return view('master.location.detail', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
