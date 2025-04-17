<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{

    public function selection(Request $request)
    {
        $items = [];
        $ref = $request->string('ref');
        if ($ref == 'menu') {
            $items = $this->_menu($request);
        } else if ($ref == 'apps_module') {
            $items = $this->_apps_module($request);
        } else if (in_array($ref, ['user_roles', 'user_status', 'apps_role', 'ticket_priority', 'ticket_status'])) {
            $items = $this->_general(['table' => $ref, 'order' => 'id ASC'], $request);
        } else if ($ref == 'pegawai') {
            $items = $this->_pegawai($request);
        } else if ($ref == 'references' && $request->filled('key')) {
            $items = $this->_general(['table' => 'm_references', 'where' => ['ref' => $request->string('key')], 'order' => 'name ASC'], $request);
        } else if ($ref == 'references') {
            $items = $this->_general(['table' => 'm_references', 'columns' => "ref AS id, UCASE(REPLACE(ref, '_', ' ')) AS name", 'group' => 'ref', 'order' => 'name ASC'], $request);
        } else if ($ref == 'action' && $request->filled('key')) {
            $items = $this->_refActions($request);
        } else if ($ref == 'jenis_pegawai') {
            $items = _getData("central", "m_pegawai_jenis", "id_jenis_pegawai AS id, nama_jenis_pegawai AS `name`", "is_deleted = 0", "`name` ASC");
        } else if ($ref == 'penugasan') {
            $items = _getData("central", "m_pegawai_penugasan", "id_penugasan AS id, nama_penugasan AS `name`", "is_deleted = 0", "`name` ASC");
        } else if (in_array($ref, ["jabatan", "penugasan", "penempatan", "unit_kerja", "unit_kerja_sub"])) {
            $table = $ref;
            if ($ref == "unit_kerja_sub") {
                $ref = "sub_unit_kerja";
            }
            $where = "is_deleted = 0";
            if ($request->has('id_unit_kerja')) {
                $where .= " AND id_unit_kerja = '" . $request->integer('id_unit_kerja') . "'";
            }
            $items = _getData("central", "m_pegawai_" . $table, "id_" . $ref . " AS id, nama_" . $ref . " AS `name`", $where, "`name` ASC");
        } else if ($ref == "sub_unit_kerja") {
            $parents = _getData("central", "m_pegawai_unit_kerja", "id_unit_kerja AS id, nama_unit_kerja AS `name`", "is_deleted = 0", "`name` ASC");
            foreach ($parents as $parent) {
                $parents = _getData("central", "m_pegawai_unit_kerja", "id_unit_kerja AS id, nama_unit_kerja AS `name`", "is_deleted = 0", "`name` ASC");
                foreach ($parents as $parent) {
                    $subs = [];
                    $_subs = _getData("central", "m_pegawai_unit_kerja_sub", "id_sub_unit_kerja AS id, nama_sub_unit_kerja AS `name`", "id_unit_kerja = '" . $parent->id . "' AND is_deleted = 0", "`name` ASC");
                    foreach ($_subs as $sub) {
                        $subs[] =
                            [
                                "id" => (int)$sub->id,
                                "name" => (string)$sub->name,
                            ];
                    }
                    $items[] =
                        [
                            "id" => (int)$parent->id,
                            "name" => (string)$parent->name,
                            "subs" => (array)$subs,
                        ];
                }
            }
        }
        return response()->json([
            'total' => COUNT($items),
            'items' => $items,
        ]);
    }

    public function download(Request $request)
    {
        if ($request->k && $request->f) {
            $file = _downloadPathUrl('uploads', $request->k, false);
            if ($file) {
                return response()->download($file, $request->f);
            }
        }
        abort(400);
    }

    public function captcha()
    {
        return response()->json(['status' => TRUE, 'message' => 'Captcha berhasil di perbarui', 'results' => captcha_img()]);
    }

    private function _menu()
    {
        $items = [];

        $_groups = DB::table('m_menu')
            ->select('id', 'group AS name')
            ->whereRaw('status = 1 AND parent = 1 AND id != 62')
            ->groupBy('group')
            ->orderBy('id', 'asc')
            ->get();
        foreach ($_groups as $group) {
            $parents = [];
            $_parents = DB::table('m_menu')
                ->selectRaw('id,name,has_sub')
                ->where('group', $group->name)
                ->whereRaw('status = 1 AND parent = 1')
                ->orderByRaw('sort,id asc')
                ->get();
            foreach ($_parents as $parent) {
                $subs = [];
                if ($parent->has_sub == 1) {
                    $_subs = DB::table('m_menu')
                        ->selectRaw('id,name')
                        ->where('parent_id', $parent->id)
                        ->whereRaw('status = 1 AND sub = 1')
                        ->orderByRaw('sort,id asc')
                        ->get();
                    foreach ($_subs as $sub) {
                        $subs[] =
                            [
                                'id' => $sub->id,
                                'name' => $sub->name,
                            ];
                    }
                }
                $parents[] =
                    [
                        'id' => $parent->id,
                        'name' => $parent->name,
                        'subs' => $subs,
                    ];
            }
            $items[] =
                [
                    'id' => $group->id,
                    'name' => ucwords($group->name),
                    'subs' => $parents,
                ];
        }

        $_groups = DB::table('m_menu')
            ->select('id', 'name')
            ->whereRaw('status = 1 AND parent = 1 AND id = 62')
            ->get();
        foreach ($_groups as $group) {
            $parents = [];
            $_parents = DB::table('m_menu')
                ->selectRaw('id,name')
                ->where('parent_id', $group->id)
                ->whereRaw('status = 1 AND sub = 1')
                ->orderByRaw('sort,id asc')
                ->get();
            foreach ($_parents as $parent) {
                $parents[] =
                    [
                        'id' => $parent->id,
                        'name' => $parent->name,
                    ];
            }
            $items[] =
                [
                    'id' => $group->id,
                    'name' => ucwords($group->name),
                    'subs' => $parents,
                ];
        }

        return $items;
    }

    private function _apps_module()
    {
        $items = [];

        $_apps = DB::table('apps_module')
            ->select('id', 'app AS name')
            ->where('status', 1)
            ->groupBy('app')
            ->orderBy('id', 'asc')
            ->get();
        foreach ($_apps as $app) {
            $pages = [];
            $_pages = DB::table('apps_module')
                ->select('id', 'page AS name')
                ->where('app', $app->name)
                ->where('status', 1)
                ->groupBy('page')
                ->orderBy('id', 'asc')
                ->get();
            foreach ($_pages as $page) {
                $modules = [];
                $_modules = DB::table('apps_module')
                    ->selectRaw('id,name')
                    ->where('app', $app->name)
                    ->where('page', $page->name)
                    ->where('status', 1)
                    ->orderBy('sort', 'asc')
                    ->get();
                foreach ($_modules as $module) {
                    $modules[] =
                        [
                            'id' => $module->id,
                            'name' => $module->name,
                        ];
                }
                $pages[] =
                    [
                        'id' => -1,
                        'name' => $page->name,
                        'subs' => $modules,
                    ];
            }
            $items[] =
                [
                    'id' => -1,
                    'name' => strtoupper($app->name),
                    'subs' => $pages,
                ];
        }

        return $items;
    }

    private function _general($array, $request)
    {
        $params = json_decode(json_encode($array));
        $query = DB::table($params->table);
        if (isset($params->columns)) {
            $query->selectRaw($params->columns);
        } else {
            $query->select('id', 'name');
        }
        if (isset($params->where)) {
            $query->where(json_decode(json_encode($params->where), true));
        }
        if ($request->has('status')) {
            $query->where('status', 1);
        }
        if (in_array($params->table, ["user_roles", "apps_role"])) {
            $query->whereRaw("id != 1");
        }
        if (isset($params->group)) {
            $query->groupBy($params->group);
        }
        if (isset($params->order)) {
            $query->orderByRaw($params->order);
        }
        return $query->get();
    }

    private function _pegawai($request)
    {
        $per_page = 10;
        $page = (int)($request->input('page') ?? 1);
        $offset = (($page - 1) * $per_page);
        $limit = ($offset + $per_page);

        $query = DB::connection('central')->table('pegawai');
        $query->selectRaw("nip_nik AS id,CONCAT(nrk_id_pjlp,' | ',nama_pegawai) AS name");
        if ($request->filled('search')) {
            $query->whereAny([
                'nip_nik',
                'nrk_id_pjlp',
                'nama_pegawai',
            ], 'LIKE', "%" . $request->input('search') . "%");
        }
        $query->whereRaw("id_status = 1 AND is_deleted = 0");
        if ($request->filled('selected')) {
            $query->whereRaw("nip_nik IN(" . $request->input('selected') . ")");
        }
        $query->orderBy('nama_pegawai', 'asc');
        $query->offset($offset);
        $query->limit($limit);
        return $query->get();
    }

    private function _refActions($request)
    {
        $data = [];
        $key = $request->integer('key');
        $ref = _singleData('default', 'm_references', 'data', "id = '" . $key . "'");
        if ($ref) {
            $raw = json_decode($ref->data);
            if ($raw) {
                $per_page = 10;
                $page = (int)($request->input('page') ?? 1);
                $offset = (($page - 1) * $per_page);
                $limit = ($offset + $per_page);

                $query = DB::connection($raw->db)->table($raw->table);
                $query->selectRaw($raw->column);
                if ($request->filled('search')) {
                    $query->whereAny($raw->search, 'LIKE', "%" . $request->input('search') . "%");
                }
                if ($request->filled('selected')) {
                    $query->where('id', $request->integer('selected'));
                }
                if (isset($raw->where)) {
                    $query->whereRaw($raw->where);
                }
                $query->orderBy('name', 'asc');
                $query->offset($offset);
                $query->limit($limit);
                return $query->get();
            }
        }
        return $data;
    }

    function _manualProcess()
    {
        // start id: 25267
        $no = 1;
        $where = "no_sosialisasi_khusus >= 25267";
        $query = _getData("esatgas", "sosialisasi_khusus", "no_sosialisasi_khusus AS id,tanggal AS `date`", $where, "id ASC");
        foreach ($query as $row) {
            $number = $no;
            if (strlen($number) == 1) {
                $number = '000' . $number;
            } else if (strlen($number) == 2) {
                $number = '00' . $number;
            } else if (strlen($number) == 3) {
                $number = '0' . $number;
            }
            $code = "SSK032025" . $number;
            _updateData("esatgas", "sosialisasi_khusus", ["kd_sosialisasi_khusus" => $code], "no_sosialisasi_khusus = '" . $row->id . "'");
            $no++;
        }
    }
}
