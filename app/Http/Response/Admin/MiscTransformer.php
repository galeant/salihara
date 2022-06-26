<?php

namespace App\Http\Response\Admin;


class MiscTransformer
{

    public static function banner($data, $message = 'Success')
    {
        // $data = $data->transform(function ($v) {
        //     $url = url($v->path);
        //     if ($v->path == NULL) {
        //         $url = NULL;
        //     }
        //     return [
        //         'url' => $url
        //     ];
        // });
        $url = NULL;
        $desc_id = NULL;
        $desc_en = NULL;
        if ($data !== NULL && $data->path !== NULL) {
            $url = url($data->path);
            $desc_id = $data->desc_id;
            $desc_en = $data->desc_en;
        }

        return response()->json([
            'message' => $message,
            'result' => [
                'url' => $url,
                'desc_id' => $desc_id,
                'desc_en' => $desc_en,
            ]
        ]);
    }

    public static function cart($data, $message = 'Success')
    {
        $data = $data->transform(function ($v) {
            $return = [];
            return $return;
        });
        return response()->json([
            'message' => $message,
            'result' => $data
        ]);
    }

    public static function about($data, $message = 'Success')
    {
        $image = NULL;
        $value_id = NULL;
        $value_en = NULL;
        if ($data  !== NULL) {
            $image = isset($data->imageBanner) ? url($data->imageBanner->path) : NULL;
            $value_id = mb_strimwidth($data->value_id, 0, 150, "...");
            $value_en = mb_strimwidth($data->value_en, 0, 150, "...");
        }
        return response()->json([
            'message' => $message,
            'result' => [
                'image' => $image,
                'value_id' => $value_id,
                'value_en' => $value_en
            ]
        ]);
    }
}
