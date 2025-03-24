<?php

namespace App\Classes;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralEmail;

class EmailController extends Controller
{

    public function send($recipients, $data)
    {
        $status = false;
        $message = __('response.no_process');
        try {
            Mail::to($recipients)->send(new GeneralEmail($data));
            $status = true;
            $message = "Email sent!";
        } catch (\Exception $e) {
            if (!app()->isProduction()) {
                $message = $e->getMessage();
            } else {
                $message = "Failed to send email!";
            }
        }
        $json["status"] = $status;
        $json["message"] = $message;
        return json_decode(json_encode($json));
    }

    public function sendNow($recipients, $data)
    {
        $status = false;
        $message = __('response.no_process');
        try {
            Mail::to($recipients)->sendNow(new GeneralEmail($data));
            $status = true;
            $message = "Email sent!";
        } catch (\Exception $e) {
            if (!app()->isProduction()) {
                $message = $e->getMessage();
            } else {
                $message = "Failed to send email!";
            }
        }
        $json["status"] = $status;
        $json["message"] = $message;
        return json_decode(json_encode($json));
    }

    public function queue($recipients, $data)
    {
        $status = false;
        $message = __('response.no_process');
        try {
            Mail::to($recipients)->queue(new GeneralEmail($data));
            $status = true;
            $message = "Email sent!";
        } catch (\Exception $e) {
            if (!app()->isProduction()) {
                $message = $e->getMessage();
            } else {
                $message = "Failed to send email!";
            }
        }
        $json["status"] = $status;
        $json["message"] = $message;
        return json_decode(json_encode($json));
    }

}
