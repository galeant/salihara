<?php

namespace App\Http\Response\Customer;

use Carbon\Carbon;

class ProgramTransformer
{

    public static function getList($data, $message = 'Success')
    {
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $items = collect($data->items())->transform(function ($v) {
                return self::reform($v, 'index');
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
                'data' => $data->transform(function ($v) {
                    return self::reform($v, 'index');
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
        return response()->json([
            'message' => $message,
            'result' => self::reform($data, 'detail')
        ]);
    }

    private static function reform($val, $type)
    {

        $desc_id = $val->desc_id;
        $desc_en = $val->desc_en;
        if ($type == 'index') {
            $desc_id = mb_strimwidth($val->desc_id, 0, 150, "...");
            $desc_en = mb_strimwidth($val->desc_en, 0, 150, "...");
        }

        $return = [
            'id' => $val->id,
            'name' => $val->name,
            'slug' => $val->slug,
            'order' => $val->order,
            'type' => $val->type,
            'schedule_unix' => $val->schedule_unix,
            'schedule_date' => Carbon::parse($val->schedule_date)->format('d-m-Y'),
            'duration_hour' => $val->duration_hour,
            'duration_minute' => $val->duration_minute,
            'desc_id' => $desc_id,
            'desc_en' => $desc_en,
            'banner' => isset($val->imageBanner) ? url($val->imageBanner->path) : NULL,
            'penampil' => [],
            'ticket' => [],
            'only_indo' => (bool)$val->only_indo,
            'video_url' => $val->video_url,
            'color' => $val->color,
        ];
        if ($type == 'index') {
            unset($return['penampil']);
            unset($return['ticket']);
            return $return;
        }

        foreach ($val->penampil as $penampil) {
            $p_desc_id = $penampil->desc_id;
            $p_desc_en = $penampil->desc_en;
            if ($type == 'index') {
                $p_desc_id = mb_strimwidth($penampil->desc_id, 0, 150, "...");
                $p_desc_en = mb_strimwidth($penampil->desc_en, 0, 150, "...");
            }
            $return['penampil'][] = [
                'id' => $penampil->id,
                'name' => $penampil->name,
                'slug' => $penampil->slug,
                'desc_id' => $p_desc_id,
                'desc_en' => $p_desc_en,
                'banner' => isset($penampil->imageBanner) ? url($penampil->imageBanner->path) : NULL,
            ];
        }

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

                'banner' => isset($ticket->imageBanner) ? url($ticket->imageBanner->path) : NULL,
            ];
        }
        return $return;
    }
}
