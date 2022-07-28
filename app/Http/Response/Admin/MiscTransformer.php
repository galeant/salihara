<?php

namespace App\Http\Response\Admin;


class MiscTransformer
{

    public static function banner($data, $message = 'Success')
    {
        $data = collect($data)->transform(function ($v) {
            $url = NULL;

            $title_id = NULL;
            $title_en = NULL;

            $sub_title_id = NULL;
            $sub_title_en = NULL;

            $desc_id = NULL;
            $desc_en = NULL;
            $hyperlink = NULL;

            if ($v !== NULL) {
                $val_id = json_decode($v->value_id);
                $val_en = json_decode($v->value_en);

                $url = (isset($v->mainImageBanner) && isset($v->mainImageBanner->path)) ? url($v->mainImageBanner->path) : NULL;

                $title_id = isset($val_id->title) ? $val_id->title : NULL;
                $title_en = isset($val_en->title) ? $val_en->title : NULL;

                $sub_title_id = isset($val_id->sub_title) ? $val_id->sub_title : NULL;
                $sub_title_en = isset($val_en->sub_title) ? $val_en->sub_title : NULL;

                $desc_id = isset($val_id->desc) ? $val_id->desc : NULL;
                $desc_en = isset($val_en->desc) ? $val_en->desc : NULL;

                $hyperlink = isset($val_id->hyperlink) ? $val_id->hyperlink : NULL;
            }

            return [
                'url' => $url,

                'title_id' => $title_id,
                'title_en' => $title_en,

                'sub_title_id' => $sub_title_id,
                'sub_title_en' => $sub_title_en,

                'desc_id' => $desc_id,
                'desc_en' => $desc_en,
                'hyperlink' => $hyperlink
            ];
        });

        return response()->json([
            'message' => $message,
            'result' => $data
        ]);
    }

    public static function about($data, $message = 'Success')
    {
        $title_id = NULL;
        $title_en = NULL;

        $sub_title_id = NULL;
        $sub_title_en = NULL;

        $desc_id = NULL;
        $desc_en = NULL;
        if ($data  !== NULL) {
            $image = (isset($data->aboutImageBanner)  && isset($data->aboutImageBanner->path)) ? url($data->aboutImageBanner->path) : NULL;
            // $value_id = $data->value_id;
            // $value_en = $data->value_en;
            // $value_id = mb_strimwidth($data->value_id, 0, 150, "...");
            // $value_en = mb_strimwidth($data->value_en, 0, 150, "...");

            $val_id = json_decode($data->value_id);
            $val_en = json_decode($data->value_en);

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
                'image' => $image,
                'title_id' => $title_id,
                'title_en' => $title_en,

                'sub_title_id' => $sub_title_id,
                'sub_title_en' => $sub_title_en,

                'desc_id' => $desc_id,
                'desc_en' => $desc_en,
            ]
        ]);
    }

    public static function faqList($data, $message = 'Success')
    {
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $items = collect($data->items())->transform(function ($v) {
                return self::faqReform($v);
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
                    return self::faqReform($v);
                }),
                'total' => count($data)
            ];
        }
        return response()->json([
            'message' => $message,
            'result' => $return
        ]);
    }

    public static function faqDetail($data, $message = 'Success')
    {
        return response()->json([
            'message' => $message,
            'result' => self::faqReform($data)
        ]);
    }

    public static function faqGrouping($data, $message = 'Succes')
    {
        $return = [];
        $group = $data->pluck('group')->unique()->sort();
        foreach ($group as $gp) {
            $return[$gp] = [
                'group' => $gp,
                'list' => []
            ];
        }
        foreach ($data as $dt) {
            $return[$dt->group]['list'][] = [
                'question' => $dt->question,
                'answer' => $dt->answer
            ];
        }
        return response()->json([
            'message' => $message,
            'result' => array_values($return)
        ]);
    }

    private static function faqReform($data)
    {
        $group = json_decode($data->group);
        $question = json_decode($data->question);
        $answer = json_decode($data->answer);
        return [
            'id' => $data->id,
            'group_id' => $group->id,
            'group_en' => $group->en,
            'question_id' => $question->id,
            'question_en' => $question->en,
            'answer_id' => $answer->id,
            'answer_en' => $answer->en
        ];
    }

    // COMMITTEE
    public static function committee($data, $message = 'Success')
    {
        $return = [
            'value_id' => null,
            'value_en' => null
        ];
        if ($data !== NULL) {
            $return = [
                'value_id' => $data->value_id,
                'value_en' => $data->value_en
            ];
        }
        return response()->json([
            'message' => $message,
            'result' => $return
        ]);
    }
    // public static function committeeList($data, $message = 'Success')
    // {
    //     if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
    //         $items = collect($data->items())->transform(function ($v) {
    //             return self::committeeReform($v);
    //         });
    //         $return = [
    //             'data' => $items,
    //             'current_page' => $data->currentPage(),
    //             'next_page_url' => $data->nextPageUrl(),
    //             'prev_page_url' => $data->previousPageUrl(),
    //             'total' => $data->total(),
    //             'total_page' => $data->lastPage(),
    //             'per_page' => $data->perPage()
    //         ];
    //     } else {

    //         $return = [
    //             'data' => $data->transform(function ($v) {
    //                 return self::committeeReform($v);
    //             }),
    //             'total' => count($data)
    //         ];
    //     }
    //     return response()->json([
    //         'message' => $message,
    //         'result' => $return
    //     ]);
    // }

    // public static function committeeDetail($data, $message = 'Success')
    // {
    //     return response()->json([
    //         'message' => $message,
    //         'result' => self::committeeReform($data)
    //     ]);
    // }

    // public static function committeeGrouping($data, $message = 'Succes')
    // {
    //     $return = [];
    //     $group = $data->pluck('division_id')->unique()->sort();
    //     foreach ($group as $gp) {
    //         $return[$gp] = [
    //             'division_id' => $gp,
    //             'names' => []
    //         ];
    //     }
    //     foreach ($data as $dt) {
    //         $return[$dt->division_id]['division_en'] = $dt->division_en;
    //         $return[$dt->division_id]['names'][] = $dt->name;
    //     }
    //     return response()->json([
    //         'message' => $message,
    //         'result' => array_values($return)
    //     ]);
    // }

    // private static function committeeReform($data)
    // {
    //     return [
    //         'id' => $data->id,
    //         'division_id' => $data->division_id,
    //         'division_en' => $data->division_en,
    //         'name' => $data->name,
    //     ];
    // }
}
