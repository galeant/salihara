<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
    protected $guarded = [];

    public function detail()
    {
        return $this->hasMany('App\TransactionDetail', 'transaction_id', 'id');
    }

    public function transactionLog()
    {
        return $this->hasMany('App\PaymentLog', 'transaction_id', 'id');
    }
}
