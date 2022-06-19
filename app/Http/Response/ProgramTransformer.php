<?php

namespace App\Http\Response;

use Carbon\Carbon;

class ProgramTransformer
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
        $return = [
            'id' => $val->id,
            'name' => $val->name,
            'slug' => $val->slug,
            'order' => $val->order,
            'schedule_unix' => $val->schedule_unix,
            'schedule_date' => Carbon::parse($val->schedule_date)->format('d-m-Y'),
            'duration_hour' => $val->duration_hour,
            'duration_minute' => $val->duration_minute,
            'desc_id' => $val->desc_id,
            'desc_en' => $val->desc_en,
            'penampil' => [],
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
