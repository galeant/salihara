<?php

namespace App\Http\Response\Customer;

use Carbon\Carbon;

class TicketTransformer
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
            'external_url' => $val->external_url,

            'price_idr' => $val->price_idr,
            'price_usd' => $val->price_usd,

            'desc_id' => $desc_id,
            'desc_en' => $desc_en,

            'snk_id' => $val->snk_id,
            'snk_en' => $val->snk_en,

            'program' => [],
            'banner' => (isset($val->imageBanner) && isset($val->imageBanner->path)) ? url($val->imageBanner->path) : NULL,
            'can_paid' => true
        ];
        foreach ($val->program as $pr) {
            $p_desc_id = $pr->desc_id;
            $p_desc_en = $pr->desc_en;
            if ($type == 'index') {
                $p_desc_id = mb_strimwidth($pr->desc_id, 0, 150, "...");
                $p_desc_en = mb_strimwidth($pr->desc_en, 0, 150, "...");
            }
            $return['program'][] = [
                'id' => $pr->id,
                'name' => $pr->name,
                'slug' => $pr->slug,
                'desc_id' => $p_desc_id,
                'desc_en' => $p_desc_en,
            ];

            if ($return['can_paid'] == true && in_array($pr->id, $access)) {
                $return['can_paid'] = false;
            }
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
