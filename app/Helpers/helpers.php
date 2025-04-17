<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('_createdBy')) {
    function _createdBy($source, $user)
    {
        $createdfrom = $source ?? 'Back Office';
        $createdby = $user ?? -1;
        $name = (in_array($createdfrom, ["Apps", "Mobile Apps", "Android Apps", "IOS Apps"]) ? "User" : "System");
        if ($createdfrom == "Back Office") {
            $icon = '<i class="fa fa-desktop"></i>';
            if ($createdby > 0) {
                $row = DB::connection('default')->table('users')->select('name')->where('id', $createdby)->first();
                if ($row) {
                    $name = $row->name;
                }
            }
            return $icon . " " . $name;
        } else if (in_array($createdfrom, ["Apps", "Mobile Apps", "Android Apps", "IOS Apps"])) {
            $icon = '<i class="fa fa-mobile-alt"></i>';
            if ($createdby > 0) {
                $row = DB::connection('central')->table('pegawai')->select('nama_pegawai')->where('nrk_id_pjlp', $createdby)->first();
                if ($row) {
                    $name = $row->nama_pegawai;
                }
            }
            return $icon . " " . $name;
        }
        return "";
    }
}

if (!function_exists('_userSimple')) {
    function _userSimple($source, $user)
    {
        $createdfrom = $source ?? 'Back Office';
        $createdby = $user ?? -1;
        $photo = config('app.placeholder.nophoto');
        $icon = '<i class="fa fa-user"></i>';
        $name = (in_array($createdfrom, ["Apps", "Mobile Apps", "Android Apps", "IOS Apps"]) ? "User" : "System");
        if ($createdfrom == "Back Office") {
            $icon = '<i class="fa fa-desktop"></i>';
            if ($createdby > 0) {
                $row = DB::connection('default')->table('users')->select('photo', 'name')->where('id', $createdby)->first();
                if ($row) {
                    $photo = _diskPathUrl('uploads', $row->photo, config('app.placeholder.nophoto'));
                    $name = $row->name;
                }
            }
        } else if (in_array($createdfrom, ["Apps", "Mobile Apps", "Android Apps", "IOS Apps"])) {
            $icon = '<i class="fa fa-mobile-alt"></i>';
            if ($createdby > 0) {
                $row = DB::connection('central')->table('pegawai')->select('photo', 'nama_pegawai')->where('nrk_id_pjlp', $createdby)->first();
                if ($row) {
                    $photo = _diskPathUrl('uploads', $row->photo, config('app.placeholder.nophoto'));
                    $name = $row->nama_pegawai;
                }
            }
        }

        $data =
            [
                "photo" => $photo,
                "icon" => $icon,
                "name" => $name,
            ];
        return json_decode(json_encode($data));
    }
}

if (!function_exists('_userAccessByScope')) {
    function _userAccessByScope($scope, $role_id)
    {
        $query = DB::table('m_menu AS a')
            ->join('user_role_privileges AS b', 'a.id', 'b.menu_id')
            ->select('b.read', 'b.create', 'b.update', 'b.delete', 'b.export', 'b.approve')
            ->whereRaw('FIND_IN_SET(?, a.scopes)', $scope)
            ->where(['a.status' => 1, 'b.role_id' => $role_id])
            ->first();
        return $query;
    }
}

if (!function_exists('_pegawaiByNip')) {
    function _pegawaiByNip($nip)
    {
        if (isset($nip)) {
            $row = DB::connection("central")->table("pegawai AS p")
                ->leftJoin("pegawai_info AS pi", "p.nip_nik", "pi.nip_nik")
                ->leftJoin("m_pegawai_penugasan AS pn", "p.id_penugasan", "pn.id_penugasan")
                ->leftJoin("m_pegawai_penempatan AS pm", "p.id_penempatan", "pm.id_penempatan")
                ->selectRaw("IFNULL(pi.photo_kasual,p.photo) AS photo, p.nip_nik, p.nrk_id_pjlp, p.nama_pegawai")
                ->selectRaw("pn.nama_penugasan,pm.nama_penempatan")
                ->whereRaw("p.nip_nik = '" . $nip . "'")
                ->first();
            if ($row) {
                // $photo = _diskPathUrl('pegawai', $row->photo, config('app.placeholder.nophoto'));
                $photo = config('filesystems.disks.pegawai.url') . $row->photo;
                $data =
                    [
                        'nip' => (int)$row->nip_nik,
                        'nrk' => (int)$row->nrk_id_pjlp,
                        'photo' => $photo,
                        'nama' => $row->nama_pegawai,
                        'penugasan' => $row->nama_penugasan,
                        'penempatan' => $row->nama_penempatan,
                    ];
                return json_decode(json_encode($data));
            }
        }
        return NULL;
    }
}

if (!function_exists('_pegawaiByNrk')) {
    function _pegawaiByNrk($nrk)
    {
        if (isset($nrk)) {
            $row = DB::connection("central")->table("pegawai AS p")
                ->leftJoin("pegawai_info AS pi", "p.nip_nik", "pi.nip_nik")
                ->leftJoin("m_pegawai_penugasan AS pn", "p.id_penugasan", "pn.id_penugasan")
                ->leftJoin("m_pegawai_penempatan AS pm", "p.id_penempatan", "pm.id_penempatan")
                ->selectRaw("IFNULL(pi.photo_kasual,p.photo) AS photo, p.nip_nik, p.nrk_id_pjlp, p.nama_pegawai")
                ->selectRaw("pn.nama_penugasan,pm.nama_penempatan")
                ->whereRaw("p.nrk_id_pjlp = '" . $nrk . "'")
                ->first();
            if ($row) {
                // $photo = _diskPathUrl('pegawai', $row->photo, config('app.placeholder.nophoto'));
                $photo = config('filesystems.disks.pegawai.url') . $row->photo;
                $data =
                    [
                        'nip' => (int)$row->nip_nik,
                        'nrk' => (int)$row->nrk_id_pjlp,
                        'photo' => $photo,
                        'nama' => $row->nama_pegawai,
                        'penugasan' => $row->nama_penugasan,
                        'penempatan' => $row->nama_penempatan,
                    ];
                return json_decode(json_encode($data));
            }
        }
        return NULL;
    }
}

if (!function_exists('_newId')) {
    function _newId($conn = 'default', $table = NULL, $column = 'id', $where = NULL)
    {
        if (isset($table)) {
            $query = DB::connection($conn)->table($table);
            if (isset($where)) {
                $query->whereRaw($where);
            }
            $id = $query->max($column);
            return ($id ?? 0) + 1;
        }
        return 1;
    }
}

if (!function_exists('_newSort')) {
    function _newSort($conn = 'default', $table = NULL, $column = 'sort', $where = NULL)
    {
        if (isset($table)) {
            $query = DB::connection($conn)->table($table);
            if (isset($where)) {
                $query->whereRaw($where);
            }
            $sort = $query->max($column);
            return ($sort ?? 0) + 1;
        }
        return 1;
    }
}

if (!function_exists('_generateNumber')) {
    function _generateNumber($id, $code)
    {
        $date = now();
        $year = substr($date, 0, 4);
        $month = _roman(substr($date, 5, 2));
        $day = substr($date, 8, 2);
        if (strlen($id) > 3) {
            return $id . '/' . $code . '/' . $day . '/' . $month . '/' . $year;
        } else if (strlen($id) > 1) {
            return '00' . $id . '/' . $code . '/' . $day . '/' . $month . '/' . $year;
        } else {
            return '000' . $id . '/' . $code . '/' . $day . '/' . $month . '/' . $year;
        }
    }
}

if (!function_exists('_generateCode')) {
    function _generateCode($conn = 'default', $table = NULL, $where = NULL, $code = '')
    {
        if (isset($table) && isset($where)) {
            $curdate = now();
            $month = $curdate->format('m');
            $year = $curdate->format('Y');
            $query = DB::connection($conn)->table($table);
            $query->whereRaw($where);
            $new_id = ($query->count() + 1);
            $number = $new_id;
            if (strlen($number) == 1) {
                $number = '000' . $number;
            } else if (strlen($number) == 2) {
                $number = '00' . $number;
            } else if (strlen($number) == 3) {
                $number = '0' . $number;
            }
            return $code . $month . $year . $number;
        }
        return "";
    }
}

if (!function_exists('_roman')) {
    function _roman($number)
    {
        if (isset($number)) {
            $number = intval($number);
            $result    = "";
            $roman     =
                array(
                    'M'  => 1000,
                    'CM' => 900,
                    'D'  => 500,
                    'CD' => 400,
                    'C'  => 100,
                    'XC' => 90,
                    'L'  => 50,
                    'XL' => 40,
                    'X'  => 10,
                    'IX' => 9,
                    'V'  => 5,
                    'IV' => 4,
                    'I'  => 1
                );

            foreach ($roman as $key => $row) {
                $matches = intval($number / $row);
                $result .= str_repeat($key, $matches);
                $number  = $number % $row;
            }
            return $result;
        }
        return "";
    }
}

if (!function_exists('_numdec')) {
    function _numdec($number = 0.00, $decimal = 2)
    {
        $number = ($number + 0);
        $explode = explode(".", $number);
        if (COUNT($explode) > 1 && $decimal > 0) {
            return (string)number_format($explode[0]) . "." . substr($explode[1], 0, $decimal);
        }
        return (string)number_format($number);
    }
}

if (!function_exists('_diskPath')) {
    function _diskPath($disk, $path, $arrays = [])
    {
        if ($path) {
            $_disk = Storage::disk($disk);
            if ($_disk->exists($path)) {
                $exp = explode("/", $path);
                $name = end($exp);
                $data =
                    [
                        "name" => $name,
                        "type" => $_disk->mimeType($path),
                        "size" => $_disk->size($path),
                        "path" => $path,
                        "url" => $_disk->url($path),
                    ];
                $collection = collect($data);
                if (COUNT($arrays)) {
                    foreach ($arrays as $key => $value) {
                        $collection->put($key, $value);
                    }
                }
                return $collection->all();
            }
        }
        return NULL;
    }
}

if (!function_exists('_diskPathUrl')) {
    function _diskPathUrl($disk, $path, $default = '')
    {
        if ($path) {
            $_disk = Storage::disk($disk);
            if ($_disk->exists($path)) {
                return $_disk->url($path);
            }
        }
        return $default;
    }
}

if (!function_exists('_removeDiskPathUrl')) {
    function _removeDiskPathUrl($disk, $path)
    {
        if ($path) {
            $_disk = Storage::disk($disk);
            if ($_disk->exists($path)) {
                return $_disk->delete($path);
            }
        }
        return false;
    }
}

if (!function_exists('_downloadPathUrl')) {
    function _downloadPathUrl($disk, $path, $default = '')
    {
        if ($path) {
            $_disk = Storage::disk($disk);
            if ($_disk->exists($path)) {
                return $_disk->path($path);
            }
        }
        return $default;
    }
}

if (!function_exists('_uuid')) {
    function _uuid()
    {
        return (string)Str::ulid();
    }
}

if (!function_exists('_number')) {
    function _number($string = '')
    {
        return Str::of($string)->replaceMatches('/[^A-Za-z0-9]++/', '');
    }
}

if (!function_exists('_strip')) {
    function _strip($string = '')
    {
        return (string)Str::of($string)->stripTags();
    }
}

if (!function_exists('_trim')) {
    function _trim($string = '')
    {
        return (string)Str::trim($string);
    }
}

if (!function_exists('_escape')) {
    function _escape($string = '', $strip = TRUE)
    {
        $output = Str::of($string)->squish();
        $output = remove_invisible_characters($output, FALSE);
        $output = addslashes($output);
        if ($strip) {
            $output = strip_tags($output);
        }
        return $output;
    }
}

if (!function_exists('_slash')) {
    function _slash($string = '', $input = FALSE)
    {
        $output = Str::of($string)->squish();
        if ($input) {
            $output = str_replace('\r\n', '&#013;', $output);
            $output = str_replace('\r', '&#013;', $output);
            $output = str_replace('\n', '&#013;', $output);
        } else {
            $output = str_replace('\r\n', '<br/>', $output);
            $output = str_replace('\r', '<br/>', $output);
            $output = str_replace('\n', '<br/>', $output);
        }
        $output = stripslashes($output);
        $output = iconv("UTF-8", "UTF-8//IGNORE", $output);
        $output = iconv("UTF-8", "ISO-8859-1//IGNORE", $output);
        $output = iconv("ISO-8859-1", "UTF-8", $output);
        return $output;
    }
}

if (!function_exists('_inputPhone')) {
    function _inputPhone($string = '')
    {
        if ($string) {
            if (substr($string, 0, 1) == "0") {
                $string = substr($string, 1, strlen($string));
            } else if (substr($string, 0, 2) == "62") {
                $string = substr($string, 2, strlen($string));
            }
        }
        return (string)$string;
    }
}

if (!function_exists('_strContains')) {
    function _strContains($string = '', $search = '')
    {
        return Str::contains($string, $search);
    }
}

if (!function_exists('_convertPhone')) {
    function _convertPhone($_phone = '')
    {
        if ($_phone) {
            $phone = preg_replace("/[^0-9]/", '', strtolower(_escape($_phone)));
            if (substr($phone, 0, 1) == "0") {
                $phone = substr($phone, 1, strlen($phone));
            } else if (substr($phone, 0, 2) == "62") {
                $phone = substr($phone, 2, strlen($phone));
            }
            return "62" . $phone;
        }
        return "";
    }
}

if (!function_exists('_convertDay')) {
    function _convertDay($days = 0)
    {
        if (isset($days)) {
            $results = "";
            if ($days >= 365) {
                $result = floor($days / 365);
                $results .= $result . " tahun ";
                $days -= ($result * 365);
            }
            if ($days >= 30) {
                $result = floor($days / 30);
                $results .= $result . " bulan ";
                $days -= ($result * 30);
            }
            if ($days > 0) {
                $result = floor($days);
                $results .= $result . " hari ";
            }
            return $results;
        }
        return "";
    }
}

if (!function_exists('_convertTime')) {
    function _convertTime($seconds = 0, bool $show_second = false)
    {
        if (isset($seconds)) {
            $results = "";
            if ($seconds >= 86400) {
                $result = floor($seconds / 86400);
                $results .= $result . " hari ";
                $seconds -= ($result * 86400);
            }
            if ($seconds >= 3600) {
                $result = floor($seconds / 3600);
                $results .= $result . " jam ";
                $seconds -= ($result * 3600);
            }
            if ($seconds >= 60) {
                $result = floor($seconds / 60);
                $results .= $result . " menit ";
                $seconds -= ($result * 60);
            }
            if ($show_second and $seconds > 0) {
                $result = floor($seconds);
                $results .= $result . " detik";
            }
            return $results;
        }
        return "";
    }
}

if (!function_exists('_maskPhone')) {
    function _maskPhone($string = "", $char = 4)
    {
        $length = strlen($string);
        if (!$length) {
            return "";
        }
        $start = ($length > $char ? ($length - $char) : ceil($length / 2));
        $end = ($length > $char ? $char : ceil($length / 2));
        return str_repeat('*', 3) . substr($string, $start, $end);
    }
}

if (!function_exists('remove_invisible_characters')) {
    function remove_invisible_characters($str, $url_encoded = TRUE)
    {
        $non_displayables = array();
        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/i';    // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/i';    // url encoded 16-31
            $non_displayables[] = '/%7f/i';    // url encoded 127
        }
        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127
        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }
}

if (!function_exists('html_escape')) {
    function html_escape($var, $double_encode = TRUE)
    {
        if (empty($var)) {
            return $var;
        }
        if (is_array($var)) {
            foreach (array_keys($var) as $key) {
                $var[$key] = html_escape($var[$key], $double_encode);
            }
            return $var;
        }
        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8', $double_encode);
    }
}

if (!function_exists('lat_lng_distance')) {
    /**
     * Calculates the distance between two points, given their
     * latitude and longitude, and returns an array of values
     * of the most common distance units
     *
     * @param  {coord} $lat1 Latitude of the first point
     * @param  {coord} $lon1 Longitude of the first point
     * @param  {coord} $lat2 Latitude of the second point
     * @param  {coord} $lon2 Longitude of the second point
     * @return {array}       Array of values in many distance units
     */
    function lat_lng_distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
    }
}

if (!function_exists('_singleData')) {
    function _singleData($conn = 'default', $table = NULL, $column = NULL, $where = NULL, $order = NULL, $limit = NULL)
    {
        if (isset($table)) {
            $query = DB::connection($conn)->table($table);
            if (isset($column)) {
                $query->selectRaw($column);
            }
            if (isset($where)) {
                $query->whereRaw($where);
            }
            if (isset($order)) {
                $query->orderByRaw($order);
            }
            if (isset($limit)) {
                $query->limit($limit);
            }
            return $query->first();
        }
        return NULL;
    }
}

if (!function_exists('_tableExist')) {
    function _tableExist($conn = 'default', $db = NULL, $table = NULL)
    {
        if (isset($db) and isset($table)) {
            $query = DB::connection($conn)
                ->select("SELECT
                            TABLE_NAME AS `data`
                        FROM INFORMATION_SCHEMA.TABLES
                        WHERE TABLE_SCHEMA = '" . $db . "'
                        AND TABLE_NAME = '" . $table . "'");
            if (COUNT($query)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('_getData')) {
    function _getData($conn = 'default', $table = NULL, $column = NULL, $where = NULL, $order = NULL, $limit = NULL)
    {
        if (isset($table)) {
            $query = DB::connection($conn)->table($table);
            if (isset($column)) {
                $query->selectRaw($column);
            }
            if (isset($where)) {
                $query->whereRaw($where);
            }
            if (isset($order)) {
                $query->orderByRaw($order);
            }
            if (isset($limit)) {
                $query->limit($limit);
            }
            return $query->get();
        }
        return [];
    }
}

if (!function_exists('_insertData')) {
    function _insertData($conn = 'default', $table = NULL, $data = NULL)
    {
        if (isset($table) && isset($data)) {
            DB::connection($conn)->table($table)->insert($data);
            return true;
        }
        return false;
    }
}

if (!function_exists('_updateData')) {
    function _updateData($conn = 'default', $table = NULL, $data = NULL, $where = NULL)
    {
        if (isset($table) && isset($data) && isset($where)) {
            DB::connection($conn)->table($table)
                ->whereRaw($where)
                ->update($data);
            return true;
        }
        return false;
    }
}

if (!function_exists('_deleteData')) {
    function _deleteData($conn = 'default', $table = NULL, $where = NULL)
    {
        if (isset($table) && isset($where)) {
            DB::connection($conn)->table($table)
                ->whereRaw($where)
                ->delete();
            return true;
        }
        return false;
    }
}

if (!function_exists('_historyTableData')) {
    function _historyTableData($request, $table, $where)
    {
        $data = DB::table($table)
            ->selectRaw("name,ip_address,user_agent")
            ->selectRaw("DATE_FORMAT(created_at, '%d %b %Y %H:%i') AS date,created_from,created_by")
            ->where($where)
            ->when($request->input('search'), function (Builder $query, string $search) {
                if ($search) {
                    $query->whereAny([
                        'name',
                        'ip_address',
                    ], 'LIKE', "%" . $search . "%");
                }
            })
            ->when($request->input('daterange'), function (Builder $query, string $search) {
                if ($search) {
                    $query->whereBetween(DB::raw('DATE(created_at)'), explode(" - ", $search));
                }
            })
            ->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('user', function ($row) {
                return ($row->created_by > 0 ? _createdBy($row->created_from, $row->created_by) : $row->name);
            })
            ->rawColumns(['user'])
            ->removeColumn(['name', 'created_from', 'created_by'])
            ->toJson();
    }
}
