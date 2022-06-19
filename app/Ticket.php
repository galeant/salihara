<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'ticket';
    protected $guarded = [];

    public function scopeLuring($q)
    {
        $q->where('type', 'luring');
    }

    public function scopeDaring($q)
    {
        $q->where('type', 'daring');
    }

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

    public function program()
    {
        return $this->belongsTo('App\Program', 'program_id', 'id');
    }
}
