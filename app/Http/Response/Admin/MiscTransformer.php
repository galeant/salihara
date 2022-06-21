<?php

namespace App\Http\Response\Admin;


class MiscTransformer
{

    public static function banner($data, $message = 'Success')
    {
        $data = $data->transform(function ($v) {
            $url = url($v->path);
            if ($v->path == NULL) {
                $url = NULL;
            }
            return [
                'url' => $url
            ];
        });
        return response()->json([
            'message' => $message,
            'result' => $data
        ]);
    }
}
