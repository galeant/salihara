<?php

namespace App\Http\Response\Customer;

use Carbon\Carbon;

class ProgramTransformer
{
    private static $user;
    private static $access = [];

    public static function getList($data, $message = 'Success')
    {
        self::getAuth();
        $access = self::$access;

        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $items = collect($data->items())->transform(function ($v) use ($access) {
                return self::reform($v, 'index', $access);
            });
            $return = [
                'data' => $items,
                'current_page' => $data->currentPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'total' => $data->total(),
                'total_page' => $data->lastPage(),
                'per_page' => $data->perPage()
            ];
        } else {

            $return = [
                'data' => $data->transform(function ($v) use ($access) {
                    return self::reform($v, 'index', $access);
                }),
                'total' => count($data)
            ];
        }
        return response()->json([
            'message' => $message,
            'result' => $return
        ]);
    }

    public static function getDetail($data, $message = 'Success')
    {
        self::getAuth();
        $access = self::$access;

        return response()->json([
            'message' => $message,
            'result' => self::reform($data, 'detail', $access)
        ]);
    }

    private static function reform($val, $type, $access)
    {

        // $desc_id = $val->desc_id;
        // $desc_en = $val->desc_en;
        // if ($type == 'index') {
        $short_desc_id = mb_strimwidth(strip_tags($val->desc_id), 0, 150, "...");
        $short_desc_en = mb_strimwidth(strip_tags($val->desc_en), 0, 150, "...");
        // }

        $video_url = NULL;
        $can_paid = true;

        // dd($video_url);
        $return = [
            'id' => $val->id,
            'name' => $val->name,
            'slug' => $val->slug,
            'order' => $val->order,
            'type' => $val->type,
            'category_id' => $val->category_id,
            'category_en' => $val->category_en,
            'schedule_id' => $val->schedule_id,
            'schedule_en' => $val->schedule_en,
            // 'schedule' => $val->schedule,
            // 'schedule_unix' => $val->schedule_unix,
            // 'schedule_date' => Carbon::parse($val->schedule_date)->format('d-m-Y H:i:s'),
            // 'duration_hour' => $val->duration_hour,
            // 'duration_minute' => $val->duration_minute,
            'desc_id' => $val->desc_id,
            'desc_en' => $val->desc_en,

            'short_desc_id' => $short_desc_id,
            'short_desc_en' => $short_desc_en,

            'banner' => (isset($val->imageBanner) && isset($val->imageBanner->path)) ? url($val->imageBanner->path) : NULL,
            'penampil' => [],
            'ticket' => [],
            'only_indo' => (bool)$val->only_indo,
            'trailer_url' => $val->trailer_url,
            'video_url' => $video_url,
            'color' => $val->color,
            'luring_url' => $val->luring_url,
            'can_paid' => $can_paid,
        ];
        foreach ($val->ticket as $ticket) {
            $t_desc_id = $ticket->desc_id;
            $t_desc_en = $ticket->desc_en;
            if ($type == 'index') {
                $p_desc_id = mb_strimwidth($ticket->desc_id, 0, 150, "...");
                $p_desc_en = mb_strimwidth($ticket->desc_en, 0, 150, "...");
            }
            $return['ticket'][] = [
                'id' => $ticket->id,
                'name' => $ticket->name,
                'slug' => $ticket->slug,
                'order' => $ticket->order,

                'price_idr' => $ticket->price_idr,
                'price_usd' => $ticket->price_usd,

                'desc_id' => $t_desc_id,
                'desc_en' => $t_desc_en,

                'snk_id' => $ticket->snk_id,
                'snk_en' => $ticket->snk_en,

                'banner' => (isset($ticket->imageBanner) && isset($val->imageBanner->path)) ? url($ticket->imageBanner->path) : NULL,
            ];

            if ($return['can_paid'] != false && in_array($ticket->id, $access)) {
                $return['video_url'] = $val->video_url;;
                $return['can_paid'] = false;
            }
        }
        // if ($type == 'index') {
        //     // unset($return['penampil']);
        //     // unset($return['ticket']);
        //     return $return;
        // }

        foreach ($val->penampil as $penampil) {
            $p_desc_id = $penampil->desc_id;
            $p_desc_en = $penampil->desc_en;
            // if ($type == 'index') {
            $short_p_desc_id = mb_strimwidth(strip_tags($penampil->desc_id), 0, 150, "...");
            $short_p_desc_en = mb_strimwidth(strip_tags($penampil->desc_en), 0, 150, "...");
            // }
            $return['penampil'][] = [
                'id' => $penampil->id,
                'name' => $penampil->name,
                'slug' => $penampil->slug,
                'desc_id' => $p_desc_id,
                'desc_en' => $p_desc_en,

                'short_desc_id' => $short_p_desc_id,
                'short_desc_en' => $short_p_desc_en,

                'banner' => (isset($penampil->imageBanner) && isset($val->imageBanner->path)) ? url($penampil->imageBanner->path) : NULL,
            ];
        }

        return $return;
    }

    private static function getAuth()
    {
        $user = auth()->user();
        if ($user !== NULL) {
            self::$access = $user->access->pluck('id')->toArray();
            self::$user = auth()->user();
        }
    }
}
