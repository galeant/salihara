<?php

namespace App\Http\Response\Admin;

use Carbon\Carbon;

class VoucherTransformer
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
                'total_page' => $data->lastPage(),
                'per_page' => $data->perPage()
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
            'code' => $val->unique_code,
            'discount' => $val->discount,
            'quota' => $val->quota,
        ];
    }
}
