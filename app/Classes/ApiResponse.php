<?php

namespace App\Classes;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ApiResponse extends Controller
{
    
    public static function rollback($e, $message = "Something went wrong! Process not completed")
    {
        DB::rollBack();
        self::throw($e, $message);
    }

    public static function throw($e, $message = "Something went wrong! Process not completed")
    {
        Log::info($e);
        throw new HttpResponseException(response()->json(["message" => $message], 500));
    }

    public static function sendResponse($results = NULL, $message = "Request success!", $code = 200)
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];
        if (!empty($results)) {
            $response['results'] = $results;
        }
        return response()->json($response, $code);
    }

    public static function sendError($message = "Request failed!", $errors = [], $code = 203)
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];
        if(!empty($errors)){
            $response['errors'] = $errors;
        }
        return response()->json($response, $code);
    }
}
