<?php

namespace App\Models\Users;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    
    public $timestamps = false;

    protected $fillable = [
        'photo',
        'ref',
        'name',
        'username',
        'email',
        'phone',
        'password',
        'role_id',
        'status_id',
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

    protected $hidden = [
        'password',
        'remember_token',
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
        'id',
        'photo',
        'ref',
        'username',
        'phone',
        'email',
        'role_id',
        'status_id',
        'is_valid',
        'status',
        'phone_verified_at',
        'email_verified_at',
        'privileges',
        'created_user',
        'updated_user',
    ];

    protected $appends = ['role', 'status', 'privileges', 'is_valid', 'created_user', 'updated_user'];

    public function scopeActive(Builder $query)
    {
        $query->where('status_id', 1);
    }

    public function getPhotoAttribute()
    {
        $photo = $this->attributes['photo'] ?? "";
        if (!$photo and $this->ref == "pegawai") {
            $photo = _singleData("central", "pegawai_info", "photo_kasual", "nip_nik = '" . $this->username . "'")->photo_kasual ?? "";
            return _diskPathUrl('pegawai', $photo, asset('assets/images/nophoto.png'));
        }
        return _diskPathUrl('uploads', $photo, asset('assets/images/nophoto.png'));
    }

    public function getRoleAttribute()
    {
        $var = UserRole::active()->find($this->role_id);
        $value = $var->name ?? "";
        return $value;
    }

    public function getStatusAttribute()
    {
        $status_id = $this->status_id ?? -1;
        $status = UserStatus::find($status_id);
        if ($status) {
            return '<span class="badge badge-light-' . $status->label . ' fw-bolder">' . $status->name . '</span>';
        }
        return "";
    }

    public function getPrivilegesAttribute()
    {
        $data = [];
        $groups = DB::table('user_role_privileges AS a')
            ->join('m_menu AS b', 'a.menu_id', 'b.id')
            ->select('b.group AS name')
            ->where('a.role_id', $this->role_id)
            ->where('a.read', 1)
            ->where(['b.parent' => 1, 'b.status' => 1])
            ->groupBy('b.group')
            ->orderBy('b.id', 'asc')
            ->get();
        foreach ($groups as $group) {
            $menus = [];
            $parents = DB::table('user_role_privileges AS a')
                ->join('m_menu AS b', 'a.menu_id', 'b.id')
                ->select('b.id', 'b.icon', 'b.name', 'b.class', 'b.url', 'b.has_sub')
                ->where('a.role_id', $this->role_id)
                ->where('a.read', 1)
                ->where(['b.parent' => 1, 'b.status' => 1, 'b.group' => $group->name])
                ->orderBy('b.id', 'asc')
                ->get();
            foreach ($parents as $parent) {
                $_subs = [];
                if ($parent->has_sub == 1) {
                    $subs = DB::table('user_role_privileges AS a')
                        ->join('m_menu AS b', 'a.menu_id', 'b.id')
                        ->select('b.id', 'b.icon', 'b.name', 'b.class', 'b.url')
                        ->where('a.role_id', $this->role_id)
                        ->where('a.read', 1)
                        ->where(['b.sub' => 1, 'b.status' => 1, 'b.parent_id' => $parent->id])
                        ->orderBy('b.id', 'asc')
                        ->get();
                    foreach ($subs as $sub) {
                        $_subs[] = $sub;
                    }
                    if (!COUNT($_subs)) {
                        continue;
                    }
                }
                $menus[] = 
                [
                    "id" => $parent->id,
                    "icon" => $parent->icon,
                    "name" => $parent->name,
                    "class" => $parent->class,
                    "url" => $parent->url,
                    "subs" => $_subs,
                ];
            }
            $data[] = 
            [
                "group" => $group->name,
                "menus" => $menus,
            ];
        }
        return json_decode(json_encode($data));
    }

    public function getIsValidAttribute()
    {
        $role = $this->getRoleAttribute();
        return (bool)($role && $this->status_id == 1);
    }

    public function getCreatedUserAttribute()
    {
        $createdfrom = $this->created_from ?? 'Back Office';
        $createdby = $this->created_by ?? -1;
        return _createdBy($createdfrom, $createdby);
    }

    public function getUpdatedUserAttribute()
    {
        $createdfrom = $this->updated_from ?? 'Back Office';
        $createdby = $this->updated_by ?? -1;
        return _createdBy($createdfrom, $createdby);
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d F Y H:i:s',
            'updated_at' => 'datetime:d F Y H:i:s',
            'phone_verified_at' => 'datetime:d F Y H:i:s',
            'email_verified_at' => 'datetime:d F Y H:i:s',
            'password' => 'hashed',
        ];
    }
}
