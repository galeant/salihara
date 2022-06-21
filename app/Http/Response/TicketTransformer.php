<?php

namespace App\Http\Response;

use Carbon\Carbon;

class TicketTransformer
{

    public static function getList($data, $message = 'Success')
    {
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $items = collect($data->items())->transform(function ($v) {
                return self::reform($v);
            });
            $return = [
                'data' => $items,
                'current_page' => $data->currentPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'total' => $data->total(),
                'total_page' => $data->lastPage()
            ];
        } else {

            $return = [
                'data' => $data->transform(function ($v) {
                    return self::reform($v);
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
            'result' => self::reform($data)
        ]);
    }

    private static function reform($val)
    {
        return [
            'id' => $val->id,
            'name' => $val->name,
            'slug' => $val->slug,
            'order' => $val->order,

            'price_idr' => $val->price_idr,
            'price_usd' => $val->price_usd,

            'desc_id' => $val->desc_id,
            'desc_en' => $val->desc_usd,

            'snk_id' => $val->snk_id,
            'snk_en' => $val->snk_en,

            'program' => [
                'id' => $val->program->id,
                'name' => $val->program->name,
                'slug' => $val->program->slug,
            ],
            'banner' => isset($val->imageBanner) ? url($val->imageBanner->path) : NULL,
        ];
    }
}
