<?php

namespace App\Http\Response\Customer;

use App\User;


class TransactionTransformer
{

    public static function cart($data, $sub_total = NULL, $total = NULL, $discount = NULL, $message = 'Success')
    {
        $data = $data->transform(function ($v) {
            $return = [
                'cart_id' => $v->id,
                'ticket_id' => $v->ticket_id,
                'ticket_name_id' => $v->ticket->name_id,
                'ticket_name_en' => $v->ticket->name_en,
                'ticket_price_idr' => $v->ticket->price_idr,
                'ticket_price_usd' => $v->ticket->price_usd,
                'type' => $v->ticket->type,
                'qty' => $v->qty,
                'program' => [],
            ];

            foreach ($v->ticket->program as $pr) {
                $return['program'][] = [
                    'program_id' => $pr->id,
                    'program_name' => $pr->name,
                    'program_schedule_id' => $pr->schedule_id,
                    'program_schedule_en' => $pr->schedule_en,
                ];
            }

            return $return;
        });
        if ($sub_total == NULL) {
            $sub_total = $data->sum('ticket_price_idr');
        }
        if ($total == NULL) {
            $total = $data->sum('ticket_price_idr');
        }

        return response()->json([
            'message' => $message,
            'result' => [
                'data' => $data,
                'sub_total' => $sub_total,
                'total' => $total,
                'discount' => $discount
            ]
        ]);
    }

    public static function getList($data, $message = 'Success')
    {
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $items = collect($data->items())->transform(function ($v) {
                return self::transactionReform($v);
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
                    return self::transactionReform($v);
                }),
                'total' => count($data)
            ];
        }
        return response()->json([
            'message' => $message,
            'result' => $return
        ]);
    }

    public static function paymentMethodList($data, $message = 'Success')
    {
        return response()->json([
            'message' => $message,
            'result' => $data
        ]);
    }

    public static function getDetail($data, $message = 'Success')
    {
        $data = self::transactionReform($data);
        return response()->json([
            'message' => $message,
            'result' => $data
        ]);
    }

    private static function transactionReform($v)
    {
        $return = [
            'id' => $v->id,
            'reff_id' => $v->reff_id,
            'gross_value_idr' => $v->gross_value_idr,
            'gross_value_usd' => $v->gross_value_usd,
            'net_value_idr' => $v->net_value_idr,
            'net_value_usd' => $v->net_value_usd,

            'voucher_code' => $v->voucher_code,
            'discount_value' => $v->discount_value,

            'checkout_id' => $v->checkout_id,
            'signature_payment' => $v->signature_payment,

            'payment_expired' => $v->payment_expired,
            'epoch_time_payment_expired' => $v->epoch_time_payment_expired,

            'payment_method_id' => $v->payment_method_id,
            'payment_method_name' => $v->payment_method_name,
            'virtual_account_assign' => $v->virtual_account_assign,
            'payment_status' => $v->payment_status,
            'created_at' => $v->created_at,
            'unix_created_at' => strtotime($v->created_at),
            'item' => []
        ];
        if (auth()->user()->role == user::ROLE[0]) {
            $return['customer']['user_id'] = $v->user_id;
            $return['customer']['user_name'] = $v->user_name;
            $return['customer']['user_email'] = $v->user_email;
            $return['customer']['user_phone'] = $v->user_phone;
            // $return['customer']['user_address'] = $v->user_address;
            // $return['customer']['province_id'] = $v->province_id;
            // $return['customer']['province_name'] = $v->province_name;
            // $return['customer']['city_id'] = $v->city_id;
            // $return['customer']['city_name'] = $v->city_name;
            // $return['customer']['district_id'] = $v->district_id;
            // $return['customer']['district_name'] = $v->district_name;
            // $return['customer']['sub_district_id'] = $v->sub_district_id;
            // $return['customer']['sub_district_name'] = $v->sub_district_name;
            // $return['customer']['postal'] = $v->postal;

            $return['log'] = $v->paymentLog->transform(function ($v) {
                return [
                    'status' => $v->status,
                    'created_at' => $v->created_at,
                    'unix_created_at' => strtotime($v->created_at)
                ];
            });
        }

        if (count($v->detail) > 0) {
            foreach ($v->detail as $dtl) {
                $tmp_detail = [
                    'ticket_id' => $dtl->ticket_id,
                    'ticket_name_id' => $dtl->ticket_name_id,
                    'ticket_name_en' => $dtl->ticket_name_en,
                    'ticket_price_idr' => $dtl->ticket_price_idr,
                    'ticket_price_usd' => $dtl->ticket_price_usd,
                    'qty' => $dtl->qty,
                    'total_price_idr' => $dtl->total_price_idr,
                    'total_price_usd' => $dtl->total_price_usd,
                    'program' => [],
                ];
                foreach ($dtl->ticket->program as $dt) {
                    $tmp_detail['program'][] = [
                        'program_slug' => $dt->slug,
                        'program_id' => $dt->id,
                        'program_name' => $dt->name,
                        'program_schedule_id' => $dt->schedule_id,
                        'program_schedule_en' => $dt->schedule_en,
                        'program_banner' => (isset($dt->imageBanner) && isset($dt->imageBanner->path)) ? url($dt->imageBanner->path) : NULL,
                    ];
                }
                $return['item'][] = $tmp_detail;
            }
        }

        return $return;
    }

    public static function access($data, $message = 'Success')
    {

        $tmp = [];
        foreach ($data as $dt) {
            $tmp_2 = [
                "ticket_id" => $dt->id,
                "ticket_name_id" => $dt->name_id,
                "ticket_name_en" => $dt->name_en,
                "ticket_price_idr" => $dt->price_idr,
                "ticket_price_usd" => $dt->price_usd,
                'program' => []
            ];
            foreach ($dt->program as $pr) {
                $tmp_2['program'][] = [
                    'program_slug' => $pr->slug,
                    'program_id' => $pr->id,
                    'program_name' => $pr->name,
                    'program_schedule_id' => $pr->schedule_id,
                    'program_schedule_en' => $pr->schedule_en,
                    'program_banner' => (isset($pr->imageBanner) && isset($pr->imageBanner->path)) ? url($pr->imageBanner->path) : NULL,
                ];
            }
            $tmp[]['item'][] = $tmp_2;
        }
        $return = [
            'data' => $tmp,
            'total' => count($data)
        ];
        return response()->json([
            'message' => $message,
            'result' => $return
        ]);
    }
}
