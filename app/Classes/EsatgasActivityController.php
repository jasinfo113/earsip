<?php

namespace App\Classes;

use App\Http\Controllers\Controller;

class EsatgasActivityController extends Controller
{

    public function save($params = NULL)
    {
        $status = false;
        $message = __('response.no_process');
        if (isset($params)) {
        }
        $json["status"] = $status;
        $json["message"] = $message;
        return json_decode(json_encode($json));
    }
}
