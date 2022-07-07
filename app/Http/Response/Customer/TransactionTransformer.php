<?php

namespace App\Http\Response\Customer;


class TransactionTransformer
{

    public static function cart($data, $message = 'Success')
    {
        $data = $data->transform(function ($v) {
            $return = [
                'cart_id' => $v->id,
                'program_id' => $v->ticket->program_id,
                'program_name' => $v->ticket->program->name,
                'ticket_id' => $v->ticket_id,
                'ticket_name' => $v->ticket->name,
                'qty' => $v->qty
            ];
            return $return;
        });
        return response()->json([
            'message' => $message,
            'result' => $data
        ]);
    }

    public static function getList($data, $message = 'Success')
    {
        $data = $data->transform(function ($v) {
            return self::transactionReform($v);
        });
        return response()->json([
            'message' => $message,
            'result' => $data
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
            'gross_value_idr' => $v->gross_value_idr,
            'gross_value_usd' => $v->gross_value_usd,
            'net_value_idr' => $v->net_value_idr,
            'net_value_usd' => $v->net_value_usd,

            'voucher_code' => $v->voucher_code,
            'discount_value' => $v->discount_value,


            'payment_expired' => $v->payment_expired,
            'epoch_time_payment_expired' => $v->epoch_time_payment_expired,

            'payment_method_id' => $v->payment_method_id,
            'payment_method_name' => $v->payment_method_name,
            'virtual_account_assign' => $v->virtual_account_assign,
            'item' => []
        ];
        foreach ($v->detail as $dtl) {
            $return['item'][] = [
                'program_id' => $dtl->program_id,
                'program_name' => $dtl->program_name,
                'ticket_id' => $dtl->ticket_id,
                'ticket_name' => $dtl->ticket_name,
                'ticket_price_idr' => $dtl->ticket_price_idr,
                'ticket_price_usd' => $dtl->ticket_price_usd,
                'qty' => $dtl->qty,
                'total_price_idr' => $dtl->total_price_idr,
                'total_price_usd' => $dtl->total_price_usd,
            ];
        }
        return $return;
    }
}
