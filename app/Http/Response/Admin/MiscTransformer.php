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

        $title_id = NULL;
        $title_en = NULL;

        $sub_title_id = NULL;
        $sub_title_en = NULL;

        $desc_id = NULL;
        $desc_en = NULL;

        if ($data !== NULL) {
            $val_id = json_decode($data->value_id);
            $val_en = json_decode($data->value_en);

            $url = isset($data->mainImageBanner) ? url($data->mainImageBanner->path) : NULL;

            $title_id = isset($val_id->title) ? $val_id->title : NULL;
            $title_en = isset($val_en->title) ? $val_en->title : NULL;

            $sub_title_id = isset($val_id->sub_title) ? $val_id->sub_title : NULL;
            $sub_title_en = isset($val_en->sub_title) ? $val_en->sub_title : NULL;

            $desc_id = isset($val_id->desc) ? $val_id->desc : NULL;
            $desc_en = isset($val_en->desc) ? $val_en->desc : NULL;
        }

        return response()->json([
            'message' => $message,
            'result' => [
                'url' => $url,

                'title_id' => $title_id,
                'title_en' => $title_en,

                'sub_title_id' => $sub_title_id,
                'sub_title_en' => $sub_title_en,

                'desc_id' => $desc_id,
                'desc_en' => $desc_en,
            ]
        ]);
    }

    public static function about($data, $message = 'Success')
    {
        $image = NULL;
        $value_id = NULL;
        $value_en = NULL;
        if ($data  !== NULL) {
            $image = isset($data->aboutImageBanner) ? url($data->aboutImageBanner->path) : NULL;
            $value_id = $data->value_id;
            $value_en = $data->value_en;
            // $value_id = mb_strimwidth($data->value_id, 0, 150, "...");
            // $value_en = mb_strimwidth($data->value_en, 0, 150, "...");
        }
        return response()->json([
            'message' => $message,
            'result' => [
                'image' => $image,
                'desc_id' => $value_id,
                'desc_en' => $value_en
            ]
        ]);
    }
}
