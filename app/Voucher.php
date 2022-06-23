<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'voucher';
    protected $guarded = [];

    public function scopeOrder($q, $order_by, $sort = 'ASC')
    {
        if (isset($order_by) && isset($sort)) {
            $q->orderBy($order_by, $sort);
        }
    }

    public function scopeSearch($q, $field, $keyword)
    {
        if (isset($field) && isset($keyword)) {
            $q->where($field, "like", "%$keyword%");
        }
    }
}
