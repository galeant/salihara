<?php

namespace App\Http\Response\Admin;

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
        // $desc_id = $val->desc_id;
        // $desc_en = $val->desc_en;
        // if ($type == 'index') {
        $short_desc_id = mb_strimwidth(strip_tags($val->desc_id), 0, 150, "...");
        $short_desc_en = mb_strimwidth(strip_tags($val->desc_en), 0, 150, "...");
        // }
        $return = [
            'id' => $val->id,
            'name' => $val->name,
            'slug' => $val->slug,
            'order' => $val->order,
            'type' => $val->type,
            'category_id' => $val->category_id,
            'category_en' => $val->category_en,
            'schedule' => $val->schedule,
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
            'only_indo' => (bool)$val->only_indo,
            'trailer_url' => $val->trailer_url,
            'video_url' => $val->video_url,
            'color' => $val->color,
            'luring_url' => $val->luring_url
        ];

        foreach ($val->penampil as $penampil) {
            $return['penampil'][] = [
                'id' => $penampil->id,
                'name' => $penampil->name,
                'slug' => $penampil->slug,
            ];
        }
        return $return;
    }
}
