<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Cart extends Model
{
    protected $table = 'cart';
    protected $guarded = [];

    public function ticket()
    {
        return $this->belongsTo('App\Ticket', 'ticket_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
