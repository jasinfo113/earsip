<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as FacadesDB;
use Yajra\DataTables\Facades\DataTables;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('master.categoris.view');
    }

    public function categori_data(Request $request)
    {
        if ($request->ajax()) {
            $query = FacadesDB::connection("default")->table("m_category");
            $data = $query->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $html = '<span class="badge badge-' . ($row->status == 1 ? "success" : "danger") . '">' . ($row->status == 1 ? "Aktif" : "Tidak Aktif") . '</span>';
                    return $html;
                })
                ->rawColumns(['status'])
                ->toJson();
        }
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
