<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\User;
use App\Models\Users\UserRole;
use App\Models\Users\UserRolePrivilege;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use DataTables;
use phpDocumentor\Reflection\Types\Null_;

class UserController extends Controller
{


    #==USERS==#
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $where = "a.role_id != 1 AND a.is_deleted = 0";
            $data = DB::table('users AS a')
                ->join('user_roles AS b', 'a.role_id', 'b.id')
                ->join('user_status AS c', 'a.status_id', 'c.id')
                ->selectRaw('a.id, a.ref, a.name, a.username, a.email, a.phone')
                ->selectRaw('b.name AS role')
                ->selectRaw('c.name AS status, c.label AS status_label')
                ->whereRaw($where)
                ->when($request->input('search'), function (Builder $query, string $search) {
                    if ($search) {
                        $query->whereAny([
                            'a.name',
                            'a.username',
                            'a.email',
                            'a.phone',
                        ], 'LIKE', "%" . $search . "%");
                    }
                })
                ->when($request->input('role_ids'), function (Builder $query, array $search) {
                    if ($search) {
                        $query->whereIn('a.role_id', $search);
                    }
                })
                ->when($request->input('status_ids'), function (Builder $query, array $search) {
                    if ($search) {
                        $query->whereIn('a.status_id', $search);
                    }
                })
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
                    $html = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'manage/user/detail\',\'id=' . $row->id . '\')" title="Detail"><i class="fa fa-th-list"></i></a>';
                    if (config('app.user_access.update', 0) == 1) {
                        $html .= ' <a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'manage/user/form\',\'id=' . $row->id . '\')" title="Update"><i class="fa fa-edit"></i></a>';
                    }
                    return $html;
                })
                ->editColumn('username', function ($row) {
                    $html = $row->username;
                    if ($row->ref == "pegawai") {
                        $html .= '<br/><span class="badge badge-light-primary fw-bolder">Pegawai</span>';
                    }
                    return $html;
                })
                ->editColumn('status', function ($row) {
                    return '<span class="badge badge-light-' . $row->status_label . ' fw-bolder">' . $row->status . '</span>';
                })
                ->rawColumns(['checkbox', 'username', 'action', 'status'])
                ->removeColumn(['ref', 'status_label'])
                ->toJson();
        }
    }

    public function form(Request $request)
    {
        if ($request->id) {
            $row = DB::table('users')->find($request->id);
            if (!$row) {
                abort(400);
            }
            $data["title"] = "Ubah User";
            $data["row"] = $row;
            $data["status"] = DB::table('user_status')->get();
        } else {
            $data["title"] = "Tambah User";
        }
        $data["roles"] = DB::table('user_roles')->whereRaw("id != 1 AND `status` = 1")->get();
        return view('manage.user.form', $data);
    }

    public function save(Request $request)
    {
        try {
            if ($request->has('id')) {
                $user = User::findOrFail($request->id);
                $request->validate([
                    'ref' => 'required|string',
                    'pegawai_id' => 'required_if:ref,pegawai|integer|nullable',
                    'name' => 'required_if:ref,user|string|min:5|nullable',
                    'username' => 'required_if:ref,user|string|min:6|unique:users,username,' . $user->id . '|nullable',
                    'email' => 'required_if:ref,user|email|unique:users,email,' . $user->id . '|nullable',
                    'phone' => 'required_if:ref,user|numeric|min_digits:8|max_digits:15|unique:users,phone,' . $user->id . '|nullable',
                    'password' => ['nullable', Password::defaults(), 'nullable'],
                    'role_id' => 'required|integer',
                    'status_id' => ['required', Rule::in([1, 9])],
                ]);
                $role = UserRole::findOrFail($request->integer('role_id'));
                $ref = strtolower($request->string('ref'));
                if ($ref == "pegawai") {
                    $pegawai = _singleData("central", "pegawai", "nip_nik,nama_pegawai,gelar_depan,gelar_belakang,email,no_telepon,`password`", "nip_nik = '" . $request->integer('pegawai_id') . "' AND id_status = 1 AND is_deleted = 0");
                    if (!$pegawai) {
                        return response()->json([
                            'status' => FALSE,
                            'message' => "Pegawai tidak ditemukan"
                        ]);
                    }
                    $where = "id != '" . $user->id . "' AND is_deleted = 0";
                    $exists = _singleData("default", "users", "`name`", $where . " AND username = '" . $pegawai->nip_nik . "'");
                    if ($exists) {
                        return response()->json([
                            'status' => FALSE,
                            'message' => "Username telah digunakan"
                        ]);
                    }
                    if ($pegawai->email) {
                        $exists = _singleData("default", "users", "`name`", $where . " AND email = '" . $pegawai->email . "'");
                        if ($exists) {
                            return response()->json([
                                'status' => FALSE,
                                'message' => "Email telah digunakan"
                            ]);
                        }
                    }
                    if ($pegawai->no_telepon) {
                        $exists = _singleData("default", "users", "`name`", $where . " AND phone = '" . $pegawai->no_telepon . "'");
                        if ($exists) {
                            return response()->json([
                                'status' => FALSE,
                                'message' => "Nomor Telepon telah digunakan"
                            ]);
                        }
                    }

                    $user->name = ($pegawai->gelar_depan ? $pegawai->gelar_depan . " " : "") . $pegawai->nama_pegawai . ($pegawai->gelar_belakang ? " " . $pegawai->gelar_belakang : "");
                    $user->username = $pegawai->nip_nik;
                    $user->email = $pegawai->email;
                    $user->phone = $pegawai->no_telepon;
                    $user->password = $pegawai->password;
                } else if ($ref == "user") {
                    $user->name = _escape($request->string('name'));
                    $user->username = _escape($request->string('username'), true);
                    $user->email = _escape($request->string('email'));
                    $user->phone = _escape($request->string('phone'));
                    if ($request->string('password')) {
                        $user->password = Hash::make($request->string('password'));
                    }
                }
                $user->ref = $ref;
                $user->role_id = $role->id;
                $user->updated_at = now();
                $user->updated_from = 'Back Office';
                $user->updated_by = Auth::user()->id;
                $user->save();
            } else {
                $request->validate([
                    'ref' => 'required|string',
                    'pegawai_id' => 'required_if:ref,pegawai|integer|nullable',
                    'name' => 'required_if:ref,user|string|min:5|nullable',
                    'username' => 'required_if:ref,user|string|min:6|unique:users,username|nullable',
                    'email' => 'required_if:ref,user|email|unique:users,email|nullable',
                    'phone' => 'required_if:ref,user|numeric|min_digits:8|max_digits:15|unique:users,phone|nullable',
                    'password' => ['required_if:ref,user', Password::defaults(), 'nullable'],
                    'role_id' => 'required',
                ]);
                $role = UserRole::findOrFail($request->integer('role_id'));
                $ref = strtolower($request->string('ref'));
                if ($ref == "pegawai") {
                    $pegawai = _singleData("central", "pegawai", "nip_nik,nama_pegawai,gelar_depan,gelar_belakang,email,no_telepon,`password`", "nip_nik = '" . $request->integer('pegawai_id') . "' AND id_status = 1 AND is_deleted = 0");
                    if (!$pegawai) {
                        return response()->json([
                            'status' => FALSE,
                            'message' => "Pegawai tidak ditemukan"
                        ]);
                    }
                    $where = "is_deleted = 0";
                    $exists = _singleData("default", "users", "`name`", $where . " AND username = '" . $pegawai->nip_nik . "'");
                    if ($exists) {
                        return response()->json([
                            'status' => FALSE,
                            'message' => "Username telah digunakan"
                        ]);
                    }
                    $where = "is_deleted = 1";
                    $exists = _singleData("default", "users", "`name`", $where . " AND username = '" . $pegawai->nip_nik . "'");
                    if ($exists) {
                        User::onlyTrashed()->where('username', $pegawai->nip_nik)->update([
                            'is_deleted' => 0,
                            'updated_at' => now(),
                            'updated_from' => 'Back Office',
                            'updated_by' => Auth::id(),
                            'deleted_at' => NULL,
                            'deleted_from' => NULL,
                            'deleted_by' => NULL,
                        ]);
                        return response()->json([
                            'status' => TRUE,
                            'message' => __(($request->id ? 'response.data_updated' : 'response.data_added')),
                        ]);
                    }
                    if ($pegawai->email) {
                        $exists = _singleData("default", "users", "`name`", $where . " AND email = '" . $pegawai->email . "'");
                        if ($exists) {
                            return response()->json([
                                'status' => FALSE,
                                'message' => "Email telah digunakan"
                            ]);
                        }
                    }
                    if ($pegawai->no_telepon) {
                        $exists = _singleData("default", "users", "`name`", $where . " AND phone = '" . $pegawai->no_telepon . "'");
                        if ($exists) {
                            return response()->json([
                                'status' => FALSE,
                                'message' => "Nomor Telepon telah digunakan"
                            ]);
                        }
                    }
                    $data['name'] = ($pegawai->gelar_depan ? $pegawai->gelar_depan . " " : "") . $pegawai->nama_pegawai . ($pegawai->gelar_belakang ? " " . $pegawai->gelar_belakang : "");
                    $data['username'] = $pegawai->nip_nik;
                    $data['email'] = $pegawai->email;
                    $data['phone'] = $pegawai->no_telepon;
                    $data['password'] = $pegawai->password;
                } else if ($ref == "user") {
                    $data['name'] = _escape($request->string('name'));
                    $data['username'] = _escape($request->string('username'), true);
                    $data['email'] = _escape($request->string('email'));
                    $data['phone'] = _escape($request->string('phone'));
                    $data['password'] = Hash::make($request->string('password'));
                }
                $data['ref'] = $ref;
                $data['role_id'] = $role->id;
                $data['remember_token'] = Str::random(100);
                $data['status_id'] = 1;
                $data['created_from'] = 'Back Office';
                $data['created_by'] = Auth::user()->id;
                User::create($data);
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
            $query = User::whereIn('id', explode(",", $request->id));
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
        $row = User::find($request->id);
        if (!$row) {
            abort(400);
        }
        $data["title"] = "Detail User";
        $data["row"] = $row;
        return view('manage.user.detail', $data);
    }


    #==ROLES==#
    public function role_data(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(UserRole::query()->where('id', '!=', 1)->orderBy('id', 'asc'))
                ->filter(function ($query) {
                    if (request('search', '')) {
                        $query->whereAny([
                            'name',
                            'description',
                        ], 'LIKE', "%" . request('search') . "%");
                    }
                })
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '';
                    if (config('app.user_access.update', 0) == 1) {
                        $html = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" href="javascript:void(0)" onclick="openForm(\'manage/user/role/form\',\'id=' . $row->id . '\')" title="Update"><i class="fa fa-edit"></i></a>';
                    }
                    return $html;
                })
                ->editColumn('status', function ($row) {
                    $html = "";
                    $html = '<div class="form-check form-switch form-check-custom form-check-solid">
                                    <input type="checkbox" class="form-check-input status-' . $row->id . '" value="' . $row->status . '" ' . ($row->status == 1 ? 'checked' : '') . ' onchange="changeStatus(this,' . $row->id . ',\'manage/user/role/status\')" />
                                </div>';
                    return $html;
                })
                ->rawColumns(['action', 'status'])
                ->toJson();
        }
    }

    public function role_form(Request $request)
    {
        if ($request->id) {
            $row = UserRole::findOrFail($request->id);
            $data["title"] = "Ubah Role";
            $data["row"] = $row;
        } else {
            $data["title"] = "Tambah Role";
        }
        return view('manage.role.form', $data);
    }

    public function role_save(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'status' => [Rule::in([0, 1])],
            ]);
            $data['name'] = $request->name;
            $data['description'] = $request->description;
            $data['status'] = $request->status;
            if ($request->id) {
                UserRole::findOrFail($request->id)->update($data);
            } else {
                UserRole::create($data);
            }
            return response()->json([
                'status' => TRUE,
                'message' => __(($request->id ? 'response.data_updated' : 'response.data_added')),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => FALSE,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function role_status(Request $request)
    {
        try {
            if (!$request->id) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.no_process'),
                ]);
            }

            $row = UserRole::findOrFail($request->id);
            if ($row->id == Auth::user()->role_id) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.failed_request'),
                ]);
            }

            $row->update([
                'status' => (int)($request->status ?? 0),
            ]);
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


    #==PRIVILEGES==#
    public function privilege_data(Request $request)
    {
        if ($request->ajax()) {
            $where = "a.role_id != 1 AND a.is_deleted = 0";
            $data = DB::table('user_role_privileges AS a')
                ->join('user_roles AS b', 'a.role_id', 'b.id')
                ->join('m_menu AS c', 'a.menu_id', 'c.id')
                ->leftJoin('m_menu AS d', 'c.parent_id', 'd.id')
                ->selectRaw('a.id, a.read, a.create, a.update, a.delete, a.export, a.approve')
                ->selectRaw('b.name AS role')
                ->selectRaw("UC_WORDS(IF(d.id IS NOT NULL, CONCAT(d.`group`,' &raquo; ',d.`name`,' &raquo; ',c.`name`),CONCAT(c.`group`,' &raquo; ',c.`name`))) AS menu")
                ->whereRaw($where)
                ->when($request->input('search'), function (Builder $query, string $search) {
                    if ($search) {
                        $query->whereAny([
                            'b.name',
                            'c.name',
                        ], 'LIKE', "%" . $search . "%");
                    }
                })
                ->when($request->input('role_ids'), function (Builder $query, array $search) {
                    if ($search) {
                        $query->whereIn('a.role_id', $search);
                    }
                })
                ->when($request->input('menu_ids'), function (Builder $query, array $search) {
                    if ($search) {
                        $query->whereIn('a.menu_id', $search);
                    }
                })
                ->groupBy("a.id")
                ->orderByRaw("d.id,c.id ASC")
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
                ->editColumn('read', function ($row) {
                    $html = '<div class="form-check form-switch form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input read-' . $row->id . '" value="' . $row->read . '" ' . ($row->read == 1 ? 'checked' : '') . ' onchange="changeStatus(this,' . $row->id . ',\'manage/user/privilege/status\',\'read\')" />
                            </div>';
                    return $html;
                })
                ->editColumn('create', function ($row) {
                    $html = '<div class="form-check form-switch form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input create-' . $row->id . '" value="' . $row->create . '" ' . ($row->create == 1 ? 'checked' : '') . ' onchange="changeStatus(this,' . $row->id . ',\'manage/user/privilege/status\',\'create\')" />
                            </div>';
                    return $html;
                })
                ->editColumn('update', function ($row) {
                    $html = '<div class="form-check form-switch form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input update-' . $row->id . '" value="' . $row->update . '" ' . ($row->update == 1 ? 'checked' : '') . ' onchange="changeStatus(this,' . $row->id . ',\'manage/user/privilege/status\',\'update\')" />
                            </div>';
                    return $html;
                })
                ->editColumn('delete', function ($row) {
                    $html = '<div class="form-check form-switch form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input delete-' . $row->id . '" value="' . $row->delete . '" ' . ($row->delete == 1 ? 'checked' : '') . ' onchange="changeStatus(this,' . $row->id . ',\'manage/user/privilege/status\',\'delete\')" />
                            </div>';
                    return $html;
                })
                ->editColumn('export', function ($row) {
                    $html = '<div class="form-check form-switch form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input export-' . $row->id . '" value="' . $row->export . '" ' . ($row->export == 1 ? 'checked' : '') . ' onchange="changeStatus(this,' . $row->id . ',\'manage/user/privilege/status\',\'export\')" />
                            </div>';
                    return $html;
                })
                ->editColumn('approve', function ($row) {
                    $html = '<div class="form-check form-switch form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input approve-' . $row->id . '" value="' . $row->approve . '" ' . ($row->approve == 1 ? 'checked' : '') . ' onchange="changeStatus(this,' . $row->id . ',\'manage/user/privilege/status\',\'approve\')" />
                            </div>';
                    return $html;
                })
                ->rawColumns(['checkbox', 'menu', 'read', 'create', 'update', 'delete', 'export', 'approve'])
                ->toJson();
        }
    }

    public function privilege_form()
    {
        $data["title"] = "Tambah Privilege";
        return view('manage.privilege.form', $data);
    }

    public function privilege_save(Request $request)
    {
        try {
            $request->validate([
                'role_id' => 'required',
                'menu_ids' => 'required',
            ]);
            $updated = 0;
            $data['role_id'] = $request->role_id;
            $data['read'] = 1;
            $data['create'] = $request->create ?? 0;
            $data['update'] = $request->update ?? 0;
            $data['delete'] = $request->delete ?? 0;
            $data['export'] = $request->export ?? 0;
            $data['approve'] = $request->approve ?? 0;
            foreach ($request->menu_ids as $r) {
                $parent_id = _singleData("default", "m_menu", "parent_id", "id = '" . $r . "' AND sub = 1 AND parent_id > 0")->parent_id ?? -1;
                if ($parent_id > 0) {
                    $exist = UserRolePrivilege::where(['role_id' =>  $request->role_id, 'menu_id' => $parent_id]);
                    if (!$exist->count()) {
                        $parent['role_id'] = $request->role_id;
                        $parent['menu_id'] = $parent_id;
                        $parent['read'] = 1;
                        $parent['created_from'] = 'Back Office';
                        $parent['created_by'] = Auth::user()->id;
                        UserRolePrivilege::create($parent);
                    }
                }
                $data['menu_id'] = $r;
                $exist = UserRolePrivilege::where(['role_id' =>  $request->role_id, 'menu_id' => $r]);
                if ($exist->count()) {
                    $input = $data;
                    $input['updated_from'] = 'Back Office';
                    $input['updated_by'] = Auth::user()->id;
                    $exist->update($input);
                } else {
                    $input = $data;
                    $input['created_from'] = 'Back Office';
                    $input['created_by'] = Auth::user()->id;
                    UserRolePrivilege::create($input);
                }
                $updated++;
            }
            return response()->json([
                'status' => TRUE,
                'message' => number_format($updated) . " " . __('response.data_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => FALSE,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function privilege_del(Request $request)
    {
        try {
            if (!$request->id) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.no_process'),
                ]);
            }
            $query = UserRolePrivilege::whereIn('id', explode(",", $request->id));
            $rows = $query->get();
            if (!$rows->count()) {
                abort(400);
            }
            $role_id = Auth::user()->role_id;
            foreach ($rows as $row) {
                if ($row->role_id == $role_id and $row->menu_id == 65) {
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
                'deleted_by' => Auth::user()->id,
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

    public function privilege_status(Request $request)
    {
        try {
            if (!$request->id or !$request->field) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.no_process'),
                ]);
            }

            $row = UserRolePrivilege::findOrFail($request->id);
            if ($row->role_id == Auth::user()->role_id and $row->menu_id == 65) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.failed_request'),
                ]);
            }
            $data[$request->field] = $request->status;
            $data['updated_at'] = now();
            $data['updated_from'] = 'Back Office';
            $data['updated_by'] = Auth::user()->id;
            $row->update($data);
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
}
