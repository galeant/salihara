<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penampil extends Model
{
    protected $table = 'penampil';
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

    public function program()
    {
        return $this->belongsToMany('App\Program', 'program_penampil', 'penampil_id', 'program_id');
    }

    public function imageBanner()
    {
        return $this->hasOne('App\Image', 'relation_id', 'id')
            ->where([
                'relation_type' => 'penampil',
                'function_type' => 'banner'
            ]);
    }
}
