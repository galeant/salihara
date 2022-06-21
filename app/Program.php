<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'program';
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

    public function penampil()
    {
        return $this->belongsToMany('App\Penampil', 'program_penampil', 'program_id', 'penampil_id');
    }

    public function ticket()
    {
        return $this->hasMany('App\Ticket', 'program_id', 'id');
    }

    public function imageBanner()
    {
        return $this->hasOne('App\Image', 'relation_id', 'id')
            ->where([
                'relation_type' => 'program',
                'function_type' => 'banner'
            ]);
    }
}
