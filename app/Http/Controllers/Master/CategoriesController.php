<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Categories;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use Yajra\DataTables\Facades\DataTables;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('master.categories.view');
    }

    public function categori_data(Request $request)
    {
        if ($request->ajax()) {
            $where = "is_deleted = 0";
            $query = DB::connection("default")->table("m_category")->whereRaw($where)
                ->when($request->input('search'), function (QueryBuilder $query, string $search) {
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
                    $html = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'master/categories/detail\',\'id=' . $row->id . '\')" title="Detail"><i class="fa fa-th-list"></i></a>';
                    if (config('app.user_access.update', 0) == 1) {
                        $html .= ' <a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'master/categories/form\',\'id=' . $row->id . '\')" title="Update"><i class="fa fa-edit"></i></a>';
                    }
                    return $html;
                })
                ->editColumn('status', function ($row) {
                    $html = '<span class="badge badge-' . ($row->status == 1 ? "success" : "danger") . '">' . ($row->status == 1 ? "Aktif" : "Tidak Aktif") . '</span>';
                    return $html;
                })
                ->addColumn('image', function ($row) {
                    $image = $row->image
                        ? _diskPathUrl('uploads', 'master/categori/' . $row->image, asset('assets/images/noimage.png'))
                        : asset('assets/images/noimage.png');

                    return '<img src="' . $image . '" width="32" height="32" class="rounded" />';
                })
                ->rawColumns(['checkbox', 'action', 'status', 'image'])
                ->toJson();
        }
    }

    public function form(Request $request)
    {
        if ($request->id) {
            $row = DB::table('m_category')->find($request->id);
            if (!$row) {
                abort(400);
            }
            $data["title"] = "Ubah Kategori";
            $data["row"] = $row;
            $data["status"] = collect([
                ["id" => 1, "name" => "Active"],
                ["id" => 2, "name" => "Non Active"]
            ]);
            $data["logo"] = _diskPathUrl('uploads', 'master/categori/' . $row->image, asset('assets/images/noimage.png'));
        } else {
            $data["title"] = "Tambah Kategori";
        }
        return view('master.categories.form', $data);
    }

    public function save(Request $request)
    {
        //dd($request->all());
        try {
            if ($request->has('id')) {
                $categori = Categories::findOrFail($request->id);
                $request->validate([
                    'kategori' => 'required|string',
                    'gambar' => $request->hasFile('gambar') ? 'required|file|mimes:jpg,jpeg,png|max:2048' : '',
                    'unit_kerja_ids' => 'required|array',
                    'penugasan_ids' => 'required|array',
                    'keterangan' => 'required|string',
                    'label' => 'required|string',
                    'status_id' => 'required',
                ]);
                $categori->name = _escape($request->string('kategori'));
                if ($request->hasFile('gambar')) {
                    $gambarBaru = $request->file('gambar')->hashName();
                    $request->file('gambar')->storeAs('public/master/categori', $gambarBaru);
                    $categori->image = $gambarBaru;
                }
                $categori->unit_kerja_ids = implode(',', array_map('htmlspecialchars', $request->input('unit_kerja_ids', [])));
                $categori->penugasan_ids = implode(',', array_map('htmlspecialchars', $request->input('penugasan_ids', [])));
                $categori->description = _escape($request->string('keterangan'));
                $categori->status = (int) $request->input('status_id');
                $categori->label = _escape($request->string('label'));
                $categori->updated_at = now();
                $categori->updated_from = 'Back Office';
                $categori->updated_by = Auth::id();
                $categori->save();
            } else {
                $request->validate([
                    'kategori' => 'required|string',
                    'gambar' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                    'unit_kerja_ids' => 'required|array',
                    'penugasan_ids' => 'required|array',
                    'label' => 'required|string',
                    'keterangan' => 'required|string',
                ]);
                $data = [];
                $data['name'] = _escape($request->string('kategori'));
                if ($request->hasFile('gambar')) {
                    $gambarBaru = $request->file('gambar')->hashName();
                    $request->file('gambar')->storeAs('public/master/categori', $gambarBaru);
                    $data['image'] = $gambarBaru;
                }
                $data['unit_kerja_ids'] = implode(',', array_map('htmlspecialchars', $request->input('unit_kerja_ids', [])));
                $data['penugasan_ids'] = implode(',', array_map('htmlspecialchars', $request->input('penugasan_ids', [])));
                $data['description'] = _escape($request->string('keterangan'));
                $data['label'] = _escape($request->string('label'));
                $data['sort'] = Categories::max('sort') + 1;
                $data['status'] = 1;
                $data['created_from'] = 'Back Office';
                $data['created_by'] = Auth::id();
                Categories::create($data);
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
            $query = Categories::whereIn('id', explode(",", $request->id));
            $rows = $query->get();
            if (!$rows->count()) {
                abort(400);
            }
            $user_id = Auth::user()->id;
            foreach ($rows as $row) {
                if ($row->id == $user_id) {
                    return response()->json([
                        'status' => FALSE,
                        'message' => __('response.failed_request'),
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
                'message' => __('response.data_deleted'),
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
        $row = Categories::find($request->id);
        if (!$row) {
            abort(400);
        }

        $id_unitkerja = explode(',', $row->unit_kerja_ids);
        $id_penugasan = explode(',', $row->penugasan_ids);
        $data["penugasan"] = _getData(
            "central",
            "m_pegawai_penugasan",
            "id_penugasan AS id, nama_penugasan AS `name`",
            "is_deleted = 0 AND id_penugasan IN (" . implode(',', $id_penugasan) . ")",
            "`name` ASC"
        );
        $data["unit_kerja"] = _getData(
            "central",
            "m_pegawai_unit_kerja",
            "id_unit_kerja AS id, nama_unit_kerja AS `name`",
            "is_deleted = 0 AND id_unit_kerja IN (" . implode(',', $id_unitkerja) . ")",
            "`name` ASC"
        );

        $data["title"] = "Detail Kategori";
        $data["row"] = $row;
        $data["logo"] = _diskPathUrl('uploads', 'master/categori/' . $row->image, asset('assets/images/noimage.png'));
        return view('master.categories.detail', $data);
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
