<?php

namespace App\Http\Response\Customer;


class PenampilTransformer
{

    public static function getList($data, $message = 'Success')
    {
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $items = collect($data->items())->transform(function ($v) {
                return self::reform($v, 'list');
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
                    return self::reform($v, 'list');
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
        // if ($type == 'list') {
        $short_desc_id = mb_strimwidth(strip_tags($val->desc_id), 0, 150, "...");
        $short_desc_en = mb_strimwidth(strip_tags($val->desc_en), 0, 150, "...");
        // }
        return [
            'id' => $val->id,
            'name' => $val->name,
            'slug' => $val->slug,
            'desc_id' => $val->desc_id,
            'desc_en' => $val->desc_en,
            'short_desc_id' => $short_desc_id,
            'short_desc_en' => $short_desc_en,
            'banner' => isset($val->imageBanner) ? url($val->imageBanner->path) : NULL,
        ];
    }
}
