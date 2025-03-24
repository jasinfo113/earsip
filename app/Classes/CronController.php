<?php

namespace App\Classes;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\Notification;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class CronController extends Controller
{


    public function siap_pending_reminder()
    {
        $updated = 0;
        $where = "u.kode_level = 16";
        $query = DB::connection("siap")
            ->table("users AS u")
            ->leftJoin(config('database.connections.default.database') . ".fcm_token AS t", function (JoinClause $join) {
                $join->on("u.username", "t.ref_id")
                    ->whereRaw("ref = 'pegawai'");
            })
            ->selectRaw("u.no_kecamatan,u.nama_kecamatan,u.username AS id,u.`name`,t.token")
            ->whereRaw($where);
        if ($query->count()) {
            $title = "Info Pending Aduan";
            $description = "Kepada Yth. [name] - Ada [total] aduan yang belum terselesaikan dan membutuhkan Tindak Lanjut dari Anda";
            foreach ($query->get() as $row) {
                $total = $this->totalTicketByStatus("t.no_kec = '" . $row->no_kecamatan . "' AND td.ticket_status_id = 1");
                if ($total > 0) {
                    $token = $row->token ?? "";
                    $notif = new Notification();
                    $notif->type = "siap";
                    $notif->platform = "damkarone";
                    $notif->title = $title;
                    $notif->message = str_replace("[name]", $row->name, str_replace("[total]", $total, $description));
                    $notif->ref = "pegawai";
                    $notif->ref_id = $row->id;
                    $notif->token = (string)$token;
                    $notif->save();

                    $updated++;
                }
            }
        }

        if ($updated) {
            echo "[" . now() . "] SIAP Weekly Reminder: " . number_format($updated) . "\n";
        }
    }

    private function totalTicketByStatus($where = NULL)
    {
        $query = DB::connection("siap")
            ->table("tickets AS t")
            ->join("ticket_details AS td", "t.id", "td.ticket_id")
            ->joinSub(function (Builder $query) {
                $query->selectRaw("ticket_id,MAX(created_at) AS created_at")
                    ->from("ticket_details")
                    ->groupBy("ticket_id");
            }, "last_detail", function (JoinClause $join) {
                $join->on("t.id", "last_detail.ticket_id")
                    ->on("td.created_at", "last_detail.created_at");
            })
            ->selectRaw("COUNT(DISTINCT t.id) AS total");
        if (isset($where)) {
            $query->whereRaw($where);
        }
        return $query->first()->total ?? 0;
    }
}
