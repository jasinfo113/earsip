<?php

namespace App\Classes;

use Google_Client;
use App\Http\Controllers\Controller;
use App\Models\Api\V1\Notification;
use Illuminate\Support\Facades\Http;

class FcmController extends Controller
{

    public function send(int $id)
    {
        $status = false;
        $message = __('response.no_process');
        $row = Notification::find($id);
        if (isset($row->id)) {
            $message_id = -1;
            if ($row->token) {
                try {
                    $payload = $this->fcmPayload($row);
                    $access_token = $this->getAccessToken();
                    if ($access_token->status) {
                        $response = $this->sendMessage($access_token->token, $payload);
                        if ($response->ok()) {
                            $message_id = $response->json('name', '-1');
                            $status =  true;
                            $message = "Data sent!";
                        } else if ($response->failed()) {
                            $message = "FCM Failed: " . $response->json('error.message', __('response.failed_request'));
                        } else {
                            $message = "FCM Error: " . __('response.failed_request');
                        }
                    } else {
                        $message = "FCM Token Error: " . $access_token->message;
                    }
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    $message = "FCM RequestException: " . $e->getMessage();
                } catch (\Exception $e) {
                    $message = "FCM Exception: " . $e->getMessage();
                }
            }
            $row->status = 1;
            $row->message_id = $message_id;
            $row->save();
        }
        $json["status"] = $status;
        $json["message"] = $message;
        return json_decode(json_encode($json));
    }

    public function cron()
    {
        $send = 0;
        $query = Notification::pending()->oldest()->take(10);
        if ($query->count()) {
            $access_token = $this->getAccessToken();
            if ($access_token->status) {
                foreach ($query->get() as $row) {
                    if (isset($row->id)) {
                        $message_id = -1;
                        if ($row->token) {
                            try {
                                $payload = $this->fcmPayload($row);
                                // return response()->json($payload, 200, [], JSON_PRETTY_PRINT);
                                $response = $this->sendMessage($access_token->token, $payload);
                                if ($response->ok()) {
                                    $message_id = $response->json('name', '-1');
                                    echo "[" . now() . "] FCM Send: #" . $row->id . " (" . $message_id . ")" . "\n";
                                } else if ($response->failed()) {
                                    echo "[" . now() . "] FCM Failed: " . $response->json('error.message', __('response.failed_request')) . "\n";
                                } else {
                                    echo "[" . now() . "] FCM Error: " . __('response.failed_request') . "\n";
                                }
                            } catch (\Illuminate\Http\Client\RequestException $e) {
                                echo "[" . now() . "] FCM RequestException: " . $e->getMessage() . "\n";
                            } catch (\Exception $e) {
                                echo "[" . now() . "] FCM Exception: " . $e->getMessage() . "\n";
                            }
                        }
                        $row->status = 1;
                        $row->message_id = $message_id;
                        $row->save();

                        $send++;
                    }
                }
            } else {
                echo "[" . now() . "] FCM Token Error: " . $access_token->message . "\n";
            }
        }
    }

    private function sendMessage($accessToken, $message)
    {
        /*
        $client = new \GuzzleHttp\Client(['timeout' => 5, 'proxy' => '10.15.3.20:80']);
        $request = $client->post($url, [
            "headers" => [
                "Authorization" => "Bearer " . $accessToken,
                "Content-Type" => "application/json",
            ],
            "json" => ['message' => $message]
        ]);
        $response = json_decode($request->getBody());
        */
        $projectId = $this->projectId();
        $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
        $response = Http::withOptions(['timeout' => 5, 'proxy' => '10.15.3.20:80'])->withToken($accessToken)->post($url, ['message' => $message]);
        return $response;
    }

    private function fcmPayload($row)
    {
        $payload = $row->ref_data;
        if (isset($payload->ref_id)) {
            $payload->ref_id = (string)$payload->ref_id;
        }
        $payload->id = (string)$row->id;
        $payload->type = (string)$row->type;
        $data['token'] = $row->token;
        $data['name'] = $row->type . '_' . $row->id;
        $data['data'] = $payload;
        $data['notification'] =
            [
                'title' => $row->title,
                'body' => $row->message,
                'image' => $row->image,
            ];
        $data['android'] =
            [
                'collapse_key' => $row->type,
                'priority' => 'HIGH',
                'data' => $payload,
                'notification' =>
                [
                    'title' => $row->title,
                    'body' => $row->message,
                    'image' => $row->image,
                    'icon' => $row->icon,
                    'sound' => $row->sound,
                    'channel_id' => ((isset($payload->ref) and $payload->ref == "kejadian") ? "channel_damkarone_important" : "channel_damkarone"), // $row->type
                    'notification_priority' => 'PRIORITY_HIGH', // [PRIORITY_DEFAULT, PRIORITY_HIGH]
                ],
            ];
        $data['apns'] =
            [
                'headers' =>
                [
                    'apns-id' => $row->type . '_' . $row->id,
                    'apns-priority' => '10',
                    'apns-topic' => $row->type,
                ],
                'payload' =>
                [
                    'aps' =>
                    [
                        'alert' =>
                        [
                            'title' => $row->title,
                            'body' => $row->message,
                        ],
                        'category' => $row->type,
                    ],
                    'messageID' => $row->type . '_' . $row->id,
                ],
                'fcm_options' =>
                [
                    'image' => $row->image,
                ],
            ];
        return $data;
    }

    private function getAccessToken()
    {
        $status = false;
        $message = "No FCM Token";
        try {
            // proxy: 10.15.3.20:80
            $auth_token = _singleData("default", "google_token", "token,refresh_token,IF(expired_at < NOW(), 1, 0) is_expired");
            $credentials_file = base_path('../_key/client_secret_691131518634-9u4bh62re5sh7nit3cq7jk790md9veok.apps.googleusercontent.com-damkarone.json');
            $httpClient = new \GuzzleHttp\Client(['timeout' => 5, 'proxy' => '10.15.3.20:80']);
            $client = new Google_Client();
            $client->setHttpClient($httpClient);
            $client->setAuthConfig($credentials_file);
            $client->setAccessType('offline');
            $client->setScopes('https://www.googleapis.com/auth/firebase.messaging');
            if ($auth_token->is_expired == 0) {
                $token = $auth_token->token;
                $data =
                    [
                        "status" => true,
                        "token" => $token,
                    ];
                return json_decode(json_encode($data));
            }
            $token = $client->fetchAccessTokenWithRefreshToken($auth_token->refresh_token);
            if (isset($token['access_token']) and $token['access_token']) {
                $data['type'] = $token['token_type'];
                $data['token'] = $token['access_token'];
                if (isset($token['refresh_token']) and $token['refresh_token'] != $auth_token->refresh_token) {
                    $data['refresh_token'] = $token['refresh_token'];
                }
                if (isset($token['scope'])) {
                    $data['scope'] = $token['scope'];
                }
                $data['expired_at'] = now()->addSecond((int)$token['expires_in']);
                _updateData("default", "google_token", $data, "id > 0");

                $data =
                    [
                        "status" => true,
                        "token" => $token['access_token'],
                    ];
                return json_decode(json_encode($data));
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $data =
            [
                "status" => $status,
                "message" => $message,
            ];
        return json_decode(json_encode($data));
    }

    private function projectId()
    {
        return "sidamkar-3b945";
    }
}
