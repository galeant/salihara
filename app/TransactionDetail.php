<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $table = 'transaction_detail';
    protected $guarded = [];

    protected $casts = [
        'ticket_price_idr' => 'integer',
        'ticket_price_usd' => 'integer',
        'total_price_idr' => 'integer',
        'total_price_usd' => 'integer',
    ];

    public function transaction()
    {
        return $this->belongsTo('App\Transaction', 'transaction_id', 'id');
    }

    public function getProgramScheduleAttribute($v)
    {
        if ($v == NULL || $v == '') {
            return [];
        }
        return json_decode($v);
    }

    // public function getTicketPriceIdrAttribute($v)
    // {
    //     return (int)$v;
    //     // if ($v !== NULL) {
    //     //     return number_format($v, 0, ",", ".");
    //     // }
    //     // return $v;
    // }

    // public function getTicketPriceUsdAttribute($v)
    // {
    //     return (int)$v;
    //     // if ($v !== NULL) {
    //     //     return number_format($v, 0, ",", ".");
    //     // }
    //     // return $v;
    // }

    // public function getTotalPriceIdrAttribute($v)
    // {
    //     return (int)$v;
    //     // if ($v !== NULL) {
    //     //     return number_format($v, 0, ",", ".");
    //     // }
    //     // return $v;
    // }
    // public function getTotalPriceUsdAttribute($v)
    // {
    //     return (int)$v;
    //     // if ($v !== NULL) {
    //     //     return number_format($v, 0, ",", ".");
    //     // }
    //     // return $v;
    // }
}
