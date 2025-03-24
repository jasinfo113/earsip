<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Classes\ApiResponse;
use App\Models\Api\ClientKey;

class ValidApiClient
{

    public function handle(Request $request, Closure $next): Response
    {
        $client_id = $request->header('client-id');
        if (!$client_id) {
            return ApiResponse::sendError(__('response.client_empty'));
        }
        $query = ClientKey::where('client_id', $client_id);
        if (!$query->count()) {
            return ApiResponse::sendError(__('response.client_invalid'));
        }
        $secret_key = $request->header('secret-key');
        if (!$secret_key) {
            return ApiResponse::sendError(__('response.key_empty'));
        }
        $query->where('secret_key', $secret_key);
        if (!$query->count()) {
            return ApiResponse::sendError(__('response.key_invalid'));
        }
        return $next($request);
    }
}
