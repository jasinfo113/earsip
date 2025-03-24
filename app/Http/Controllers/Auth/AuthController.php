<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{

    public function index()
    {
        return view('auth.login');
    }

    public function validate(AuthRequest $request)
    {
        $this->_validRequest($request);
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
                'captcha' => 'required|captcha',
            ], [
                'captcha.required' => 'Captcha wajib di isi',
                'captcha.captcha' => 'Captcha tidak benar',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => FALSE,
                    'message' => implode("\n", $validator->errors()->all())
                ]);
            }

            $request->authenticate();
            $request->session()->regenerate();

            $user = $request->user();
            $token = $request->session()->token();
            $ip_address = $request->ip();
            $user_agent = $request->userAgent();

            $d_login['ref'] = "user";
            $d_login['ref_id'] = $user->id;
            $d_login['token'] = $token;
            $d_login['ip_address'] = $ip_address;
            $d_login['user_agent'] = $user_agent;
            $d_login['created_from'] = "Website";
            _insertData("default", "login_history", $d_login);

            return response()->json([
                'status' => TRUE,
                'message' => __('response.login_success'),
                'url' => route('dashboard')
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

    public function forgot()
    {
        return view('auth.forgot-password');
    }

    public function forgot_save(Request $request)
    {
        $this->_validRequest($request);
        try {
            $request->validate(
                [
                    'email' => 'required|email',
                ]
            );
            $email = $request->email;
            $user = _singleData("default", "users", "id,email,status_id", "email = '" . $email . "' AND email IS NOT NULL AND is_deleted = 0");
            if (!$user) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('auth.email')
                ]);
            }
            if ($user->status_id != 1) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('auth.suspend')
                ]);
            }

            $recovery_id = _newId("default", "password_recovery");
            $code = Str::random(35);
            $expired_at = now()->addMinute(30);
            $url = "https://pemadam.jakarta.go.id/damkarone/reset-password/" . md5($recovery_id . $code);
            $_email = [
                'subject' => 'Reset Password',
                'title' => 'Reset Password',
                'content' => 'email.auth.reset_password',
                'expired_at' => $expired_at->format('d F Y H:i'),
                'url' => $url,
            ];
            $request = app(\App\Classes\EmailController::class)->sendNow($user->email, $_email);
            if (!$request->status) {
                return response()->json([
                    'status' => FALSE,
                    'message' => $request->message
                ]);
            }

            $d_recovery['id'] = $recovery_id;
            $d_recovery['ref'] = "user";
            $d_recovery['ref_id'] = $user->id;
            $d_recovery['email'] = $email;
            $d_recovery['code'] = $code;
            $d_recovery['expired_at'] = $expired_at;
            $d_recovery['status'] = 0;
            $d_recovery['created_from'] = "Apps";
            _insertData("default", "password_recovery", $d_recovery);

            return response()->json([
                'status' => TRUE,
                'message' => __('response.forgot_success')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => FALSE,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            if (!app()->isProduction()) {
                return response()->json([
                    'status' => FALSE,
                    'message' => $e->getMessage()
                ]);
            }
            return response()->json([
                'status' => FALSE,
                'message' => __('response.failed_request')
            ]);
        }
    }

    public function reset_password(Request $request)
    {
        if ($request->key) {
            $key = $request->key;
            $row = _singleData("default", "password_recovery", "email,DATE_FORMAT(expired_at, '%d %M %Y %H:%i') AS expired_at,`status`,IF(expired_at > NOW() AND `status` = 0, 1, 0) AS is_valid,IF(NOW() > expired_at, 1, 0) is_expired", "MD5(CONCAT(id,`code`)) = '" . $key . "'");
            $data["key"] = $key;
            $data["row"] = $row;
            return view('auth.reset-password', $data);
        }
        abort(404);
    }

    public function reset_password_save(Request $request)
    {
        $this->_validRequest($request);
        try {
            $request->validate([
                'key' => ['required', 'string'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $key = $request->string('key');
            $row = _singleData("default", "password_recovery", "*", "MD5(CONCAT(id,`code`)) = '" . $key . "' AND expired_at > NOW() AND `status` = 0");
            if (!$row) {
                return response()->json([
                    'status' => FALSE,
                    'message' => __('response.data_invalid')
                ]);
            }

            return response()->json([
                'status' => FALSE,
                'message' => "valid"
            ]);

            if ($row->ref == "user" and $row->ref_id > 0) {
                $d_user['password'] = Hash::make($request->password);
                $d_user['remember_token'] = Str::random(100);
                _updateData("default", "users", $d_user, "id = '" . $row->ref_id . "'");

                $d_history['user_id'] = $row->ref_id;
                $d_history['description'] = "User melakukan perubahan password";
                $d_history['ip_address'] = $request->ip();
                $d_history['user_agent'] = $request->userAgent();
                $d_history['created_from'] = "Web";
                $d_history['created_by'] = -1;
                _insertData("default", "user_history", $d_history);
            } else if ($row->ref == "pegawai" and $row->ref_id > 0) {
                $d_pegawai['password'] = Hash::make($request->password);
                $d_pegawai['updated_at'] = now();
                $d_pegawai['updated_from'] = "Web";
                $d_pegawai['updated_by'] = -1;
                _updateData("central", "pegawai", $d_pegawai, "nip_nik = '" . $row->ref_id . "'");

                $d_history['nip_nik'] = $row->ref_id;
                $d_history['description'] = "Pegawai melakukan perubahan password";
                $d_history['ip_address'] = $request->ip();
                $d_history['user_agent'] = $request->userAgent();
                $d_history['created_from'] = "Web";
                $d_history['created_by'] = -1;
                _insertData("central", "pegawai_history", $d_history);
            }

            $d_recovery['ref'] = $row->ref;
            $d_recovery['ref_id'] = $row->ref_id;
            $d_recovery['email'] = $row->email;
            $d_recovery['code'] = $row->code;
            $d_recovery['expired_at'] = $row->expired_at;
            $d_recovery['status'] = 1;
            $d_recovery['created_at'] = $row->created_at;
            $d_recovery['created_from'] = $row->created_from;
            $d_recovery['updated_from'] = "Web";
            _insertData("default", "password_recovery_history", $d_recovery);

            _deleteData("default", "password_recovery", "id = '" . $row->id . "'");

            $_email = [
                'subject' => 'Informasi Perubahan Password',
                'title' => 'Informasi Perubahan Password',
                'content' => 'email.auth.change_password',
            ];
            app(\App\Classes\EmailController::class)->queue($row->email, $_email);
            return response()->json([
                'status' => TRUE,
                'message' => __($status),
                'url' => route('auth/login?email=' . $row->email)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => FALSE,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function email_verification(Request $request)
    {
        if ($request->key && $request->filled('ref')) {
            $key = $request->key;
            $ref = $request->string('ref');
            if ($ref == "user") {
                $row = _singleData("default", "users", "id,email_verified_at", "MD5(CONCAT(id,remember_token)) = '" . $key . "'");
            } else if ($ref == "pegawai") {
                $row = _singleData("central", "pegawai", "nip_nik AS id,email_verified_at", "MD5(CONCAT(nip_nik,nrk_id_pjlp)) = '" . $key . "'");
            }
            if (isset($row)) {
                if (!$row->email_verified_at) {
                    if ($ref == "user") {
                        _updateData("default", "users", ["email_verified_at" => now()], "id = '" . $row->id . "'");

                        $d_history['user_id'] = $row->id;
                        $d_history['description'] = "Email telah terverifikasi";
                        $d_history['ip_address'] = $request->ip();
                        $d_history['user_agent'] = $request->userAgent();
                        $d_history['created_from'] = "Web";
                        $d_history['created_by'] = -1;
                        _insertData("default", "user_history", $d_history);
                    } else if ($ref == "pegawai") {
                        _updateData("central", "pegawai", ["email_verified_at" => now()], "nip_nik = '" . $row->id . "'");

                        $d_history['nip_nik'] = $row->id;
                        $d_history['description'] = "Email telah terverifikasi";
                        $d_history['ip_address'] = $request->ip();
                        $d_history['user_agent'] = $request->userAgent();
                        $d_history['created_from'] = "Web";
                        $d_history['created_by'] = -1;
                        _insertData("central", "pegawai_history", $d_history);
                    }
                }
                $data["row"] = $row;
                return view('auth.email-verification');
            }
        }
        abort(404);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function sso_auth(Request $request)
    {
        try {
            if (!$request->has('auth_code')) {
                $data['message'] = __('Tidak ada data untuk di proses');
                return view('errors.auth', $data);
            }
            $url = "https://pemadam.jakarta.go.id/sso/api/v1/auth-client";
            $auth_code = $request->string('auth_code');
            $client = new \GuzzleHttp\Client(['timeout' => 5, 'proxy' => '10.15.3.20:80']);
            $guzzle = $client->post($url, [
                "headers" => [
                    "Content-Type" => "application/json",
                    "client-id" => "sidamk-TXSTcc1goJWtSy0",
                    "client-secret" => "2fSNsN8e7TjmsLBSz4oQ5Pg8MblcbwMxCHq93cgFuf4XcaKlvKc8K9BwRlsBlyeP",
                ],
                "json" => ['auth_code' => $auth_code]
            ]);
            $response = json_decode($guzzle->getBody());
            if ($response->status) {
                $results = $response->results;
                $pegawai = $results->data;

                $user = User::whereRaw("username = '" . $pegawai->nip . "' AND ref = 'pegawai'")->first();
                if (!$user) {
                    $data['message'] = __('Anda tidak memiliki akses');
                    return view('errors.auth', $data);
                }
                if (!$user->is_valid) {
                    $data['message'] = trans('auth.suspend');
                    return view('errors.auth', $data);
                }
                Auth::login($user, true);

                $user = $request->user();
                $token = $request->session()->token();
                $ip_address = $request->ip();
                $user_agent = $request->userAgent();

                $d_login['ref'] = "user";
                $d_login['ref_id'] = $user->id;
                $d_login['token'] = $token;
                $d_login['ip_address'] = $ip_address;
                $d_login['user_agent'] = $user_agent;
                $d_login['created_from'] = "SSO";
                _insertData("default", "login_history", $d_login);

                return redirect()->route('dashboard');
            } else {
                $data['message'] = $response->message;
                return view('errors.auth', $data);
            }
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return view('errors.auth', $data);
        }
    }

    private function _validRequest(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'status' => FALSE,
                'message' => __('response.no_process')
            ]);
        }
        if (Auth::check()) {
            return response()->json([
                'status' => TRUE,
                'message' => __('response.login_already'),
                'url' => route('dashboard')
            ]);
        }
    }
}
