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
            'desc_id' => $val->desc_id,
            'desc_en' => $val->desc_en,
            'banner' => isset($val->imageBanner) ? url($val->imageBanner->path) : NULL,
            'penampil' => [],
            'ticket' => [],
            'only_indo' => (bool)$val->only_indo,
            'video_url' => $val->video_url,
        ];
        if ($type == 'index') {
            unset($return['penampil']);
            unset($return['ticket']);
            return $return;
        }

        foreach ($val->penampil as $penampil) {
            $return['penampil'][] = [
                'id' => $penampil->id,
                'name' => $penampil->name,
                'slug' => $penampil->slug,
                'desc_id' => $penampil->desc_id,
                'desc_en' => $penampil->desc_en,
                'banner' => isset($penampil->imageBanner) ? url($penampil->imageBanner->path) : NULL,
            ];
        }

        foreach ($val->ticket as $ticket) {
            $return['ticket'][] = [
                'id' => $ticket->id,
                'name' => $ticket->name,
                'slug' => $ticket->slug,
                'order' => $ticket->order,

                'price_idr' => $ticket->price_idr,
                'price_usd' => $ticket->price_usd,

                'desc_id' => $ticket->desc_id,
                'desc_en' => $ticket->desc_usd,

                'snk_id' => $ticket->snk_id,
                'snk_en' => $ticket->snk_en,

                'banner' => isset($ticket->imageBanner) ? url($ticket->imageBanner->path) : NULL,
            ];
        }
        return $return;
    }
}
