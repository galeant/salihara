<?php

namespace App\Http\Response\Admin;

use Carbon\Carbon;

class CustomerTransformer
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
            'result' => self::reform($data, 'detail')
        ]);
    }

    private static function reform($val, $type = NULL)
    {
        $return = [
            'id' => $val->id,
            'name' => $val->name,
            'email' => $val->email,
            'phone' => $val->phone,
            // 'address' => $val->address,
            'disabled' => (bool)$val->is_disabled,
        ];

        if ($type == 'detail') {
            $return['access'] = $val->access->transform(function ($v) {
                $ret = [
                    'id' => $v->id,
                    'name_id' => $v->name_id,
                    'name_en' => $v->name_en,
                    'program' => []
                ];
                foreach ($v->program as $prg) {
                    $ret['program'][] = [
                        'id' => $prg->id,
                        'name' => $prg->name,
                    ];
                }

                return $ret;
            });
        }

        return $return;
    }
}
