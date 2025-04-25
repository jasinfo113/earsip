<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Main\Archives;
use App\Models\Main\Document_history;
use App\Models\Main\Document_status;
use App\Models\Main\Document_file;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class ArchivesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('main.archives.view');
    }

    public function archives_data(Request $request)
    {
        if ($request->ajax()) {
            $where = "is_deleted = 0";
            $query = DB::connection("default")->table("document")->whereRaw($where)
                ->when($request->input('search'), function (Builder $query, string $search) {
                    if ($search) {
                        $query->whereAny([
                        'title',
                        'number',
                        'date',
                        'description',
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
                $html = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'main/archives/detail\',\'id=' . $row->id . '\')" title="Detail"><i class="fa fa-th-list"></i></a>';
                    if (config('app.user_access.update', 0) == 1) {
                    $html .= ' <a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'main/archives/form\',\'id=' . $row->id . '\')" title="Update"><i class="fa fa-edit"></i></a>';
                    }
                    return $html;
                })
                ->editColumn('status', function ($row) {
                $html = '<span class="badge badge-' . ($row->status_id == 1 ? "success" : "danger") . '">' . ($row->status_id == 1 ? "Aktif" : "Tidak Aktif") . '</span>';
                    return $html;
                })

                ->rawColumns(['checkbox', 'action', 'status'])
                ->toJson();
        }
    }

    public function form(Request $request)
    {
        if ($request->id) {
            $row = Archives::with('document_files')->find($request->id);
            if (!$row) {
                abort(400);
            }
            $data["title"] = "Ubah Asrip";
            $data["row"] = $row;
            $data["status"] = Document_status::select('id', 'name')->get();
        } else {
            $data["title"] = "Tambah Arsip";
            $data["row"] = null;
        }
        return view('main.archives.form', $data);
    }

    public function save(Request $request)
    {
        try {
            if ($request->has('id')) {
                // UPDATE arsip
                $arsip = Archives::findOrFail($request->id);

                $request->validate([
                    'file'         => 'nullable|file|mimes:pdf|max:5048',
                    'date'         => 'required|date',
                    'title'        => 'required|string',
                    'category_id'  => 'required|string',
                    'tag_ids'      => 'required|array',
                    'location_id'  => 'required|string',
                    'keterangan'   => 'required|string',
                    'note'         => 'required|string',
                ]);

                $arsip->update([
                    'ref_number'    => _escape($request->input('ref_nomor')),
                    'tag_ids'       => implode(',', array_map('htmlspecialchars', $request->input('tag_ids', []))),
                    'date'          => $request->input('date') . date(' H:i:s'),
                    'description'   => _escape($request->input('keterangan')),
                    'note'          => _escape($request->input('note')),
                    'title'         => _escape($request->input('title')),
                    'category_id'   => _escape($request->input('category_id')),
                    'location_id'   => _escape($request->input('location_id')),
                    'updated_from'  => 'Back Office',
                    'updated_by'    => Auth::id(),
                ]);

                // Jika ada file PDF baru
                if ($request->hasFile('file')) {
                    $pdfFile = $request->file('file');
                    $namaFilePdf = $pdfFile->hashName();
                    $pdfFile->storeAs('public/main/arsip', $namaFilePdf);

                    Document_file::create([
                        'document_id'   => $arsip->id,
                        'name'          => $namaFilePdf,
                        'description'   => _escape($request->input('keterangan')),
                        'sort'          => Document_file::where('document_id', $arsip->id)->max('sort') + 1,
                        'created_from'  => 'Back Office',
                        'created_by'    => Auth::id(),
                    ]);
                }

                Document_history::create([
                    'document_id'   => $arsip->id,
                    'description'   => 'Arsip diperbarui',
                    'ip_address'    => $request->ip(),
                    'user_agent'    => $request->header('User-Agent'),
                    'created_from'  => 'Back Office',
                    'created_by'    => Auth::id(),
                ]);
            } else {
                // INSERT arsip baru
                $request->validate([
                    'file'         => 'required|file|mimes:pdf|max:5048',
                    'date'         => 'required|date',
                    'title'        => 'required|string',
                    'category_id'  => 'required|string',
                    'tag_ids'      => 'required|array',
                    'location_id'  => 'required|string',
                    'keterangan'   => 'required|string',
                    'note'         => 'required|string',
                ]);

                $data = [];
                $code = generateKodeAcak();
                $data['code']         = $code;
                $data['number']       = generateNomorArsip();
                $data['ref_number']   = _escape($request->input('ref_nomor'));
                $data['tag_ids']      = implode(',', array_map('htmlspecialchars', $request->input('tag_ids', [])));
                $data['date']         = $request->input('date') . date(' H:i:s');
                $data['description']  = _escape($request->input('keterangan'));
                $data['note']         = _escape($request->input('note'));
                $data['title']        = _escape($request->input('title'));
                $data['category_id']  = _escape($request->input('category_id'));
                $data['location_id']  = _escape($request->input('location_id'));
                $data['status_id']    = 1;
                $data['created_from'] = 'Back Office';
                $data['created_by']   = Auth::id();

                $document = Archives::create($data);
                $document_id = $document->id;

                if ($request->hasFile('file')) {
                    $pdfFile = $request->file('file');
                    $namaFilePdf = $pdfFile->hashName();
                    $path = $pdfFile->storeAs('public/main/arsip', $namaFilePdf);
                    $fullUrl = asset('uploads/main/arsip/' . $namaFilePdf);
                }

                Document_file::create([
                    'document_id'   => $document_id,
                    'name'          => $namaFilePdf,
                    'description'   => _escape($request->input('keterangan')),
                    'sort'          => Document_file::max('sort') + 1,
                    'created_from'  => 'Back Office',
                    'created_by'    => Auth::id(),
                ]);

                Document_history::create([
                    'document_id'   => $document_id,
                    'description'   => 'Arsip baru ditambahkan',
                    'ip_address'    => $request->ip(),
                    'user_agent'    => $request->header('User-Agent'),
                    'created_from'  => 'Back Office',
                    'created_by'    => Auth::id(),
                ]);
            }

            return response()->json([
                'status' => TRUE,
                'message' => __(($request->id ? 'response.data_updated' : 'response.data_added')),
                'modal' => 'modal',
                'data' => array(
                    'nama_file' => $fullUrl,
                    'code' => $code,
                ),
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

    public function pembubuhan(Request $request)
    {
        $data["nama_file"] = $request->nama_file;
        $data["code"] = $request->code;
        $data["title"] = "Pembubuhan";
        $data["row"] = null;

        return view('main.archives.pembubuhan', $data);
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
            $query = Archives::whereIn('id', explode(",", $request->id));
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
        $row = Archives::select(
            'document.id as id',
            'document.number as number',
            'document.ref_number as ref_number',
            'document.date as date',
            'document.title as title',
            'document.description as description',
            'document.note as note',
            'document.tag_ids as tag_ids',
            'document.created_at as created_at',
            'document.created_from as created_from',
            'document.status_id as status',
            'm_category.name as category_name',
            'm_location.name as location_name',
            'document_file.name as file',
            'document.updated_at as updated_at',
            'document.updated_from as updated_from',
            'document.updated_by as updated_by',
        )
            ->Join('m_category', 'document.category_id', '=', 'm_category.id')
            ->Join('m_location', 'document.location_id', '=', 'm_location.id')
            ->Join('document_file', 'document.id', '=', 'document_file.document_id')
            ->where('document.id', $request->id)
            ->first();
        $tag = explode(',', $row->tag_ids);
        $data["tags"] = _getData(
            "default",
            "m_tag",
            "id, `name`",
            "is_deleted = 0 AND id IN (" . implode(',', $tag) . ")",
            "`name` ASC"
        );
        if (!$row) {
            abort(400);
        }
        $data["title"] = "Detail Arsip";
        $data["row"] = $row;
        return view('main.archives.detail', $data);
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
