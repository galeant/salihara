<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Transaction extends Model
{
    protected $table = 'transaction';
    protected $guarded = [];
    protected $casts = [
        'gross_value_idr' => 'integer',
        'gross_value_usd' => 'integer',
        'net_value_idr' => 'integer',
        'net_value_usd' => 'integer',
        'epoch_time_payment_expired' => 'integer',
    ];

    private function getField()
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        return $columns;
    }

    public function scopeOrder($q, $order_by, $sort)
    {
        $column = $this->getField();
        if (is_array($order_by) && is_array($sort)) {
            $max = count($order_by);
            if (count($sort) > $max) {
                $max = count($sort);
            }
            for ($i = 0; $i < $max; $i++) {
                if (in_array($order_by[$i], $column) && isset($order_by[$i]) && isset($sort[$i])) {
                    $q->orderBy($order_by[$i], $sort[$i]);
                }
            }
        }
    }

    public function scopeSearch($q, $field, $keyword)
    {
        $column = $this->getField();
        if (is_array($field) && is_array($keyword)) {
            $max = count($field);
            if (count($keyword) > $max) {
                $max = count($keyword);
            }
            for ($i = 0; $i < $max; $i++) {
                if (in_array($field[$i], $column) && isset($field[$i]) && isset($keyword[$i])) {
                    $q->where($field[$i], $keyword[$i]);
                }
            }
        }
    }

    public function customer()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function detail()
    {
        return $this->hasMany('App\TransactionDetail', 'transaction_id', 'id');
    }

    public function paymentLog()
    {
        return $this->hasMany('App\PaymentLog', 'transaction_id', 'id')->orderBy('created_at', 'asc');
    }

    // public function getEpochTimePaymentExpiredAttribute($v)
    // {
    //     return (int)$v;
    // }

    // public function getGrossValueIdrAttribute($v)
    // {
    //     return (int)$v;
    //     // if ($v !== NULL) {
    //     //     return number_format($v, 0, ",", ".");
    //     // }
    //     // return $v;
    // }

    // public function getGrossValueUsdAttribute($v)
    // {
    //     return (int)$v;
    //     // if ($v !== NULL) {
    //     //     return number_format($v, 0, ",", ".");
    //     // }
    //     // return $v;
    // }

    // public function getNetValueIdrAttribute($v)
    // {
    //     return (int)$v;
    //     // if ($v !== NULL) {
    //     //     return number_format($v, 0, ",", ".");
    //     // }
    //     // return $v;
    // }

    // public function getNetValueUsdAttribute($v)
    // {
    //     return (int)$v;
    //     // if ($v !== NULL) {
    //     //     return number_format($v, 0, ",", ".");
    //     // }
    //     // return $v;
    // }
}
