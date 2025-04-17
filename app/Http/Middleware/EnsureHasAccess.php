<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Classes\ApiResponse;
use App\Models\Api\ApiKey;

class EnsureHasAccess
{

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/v1/auth/*')) {
            $key = $request->header('secret-key');
            if (!$key) {
                return ApiResponse::sendError(__('response.key_empty'));
            }
            $key = ApiKey::firstWhere('key', $key);
            if (!$key) {
                return ApiResponse::sendError(__('response.key_invalid'));
            }
        } else if ($request->is('api/*')) {
            $key = $request->header('secret-key');
            if (!$key) {
                return ApiResponse::sendError(__('response.key_empty'));
            }
            $key = ApiKey::firstWhere('key', $key);
            if (!$key) {
                return ApiResponse::sendError(__('response.key_invalid'));
            }
            $token = $request->header('Authorization');
            if (!$token) {
                return ApiResponse::sendError(__('response.token_empty'));
            }
            $user = auth('employee')->user();
            if (!$user) {
                return ApiResponse::sendError(__('response.exception_401'), [], 401);
            }
            if (!$user->is_valid) {
                return ApiResponse::sendError(__('response.exception_403'), [], 403);
            }
            Auth::setUser($user);
        } else {
            $user = auth('web')->user();
            if (!$user) {
                return redirect('auth/login');
            }
            if (!$user->is_valid) {
                return redirect('auth/login');
            }
            $scope = Route::currentRouteName() ?? -1;
            $access = _userAccessByScope($scope, $user->role_id);
            //dd($access);
            if (!($access->read ?? 0)) {
                abort(403);
            }
            $configs["app.user_access.create"] = $access->create;
            $configs["app.user_access.update"] = $access->update;
            $configs["app.user_access.delete"] = $access->delete;
            $configs["app.user_access.export"] = $access->export;
            $configs["app.user_access.approve"] = $access->approve;
            config($configs);
        }
        return $next($request);
    }
}
