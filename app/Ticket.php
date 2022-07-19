<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;


class Ticket extends Model
{
    const type = [
        'daring',
        'external'
    ];
    protected $table = 'ticket';
    protected $guarded = [];
    protected $casts = [
        'price_idr' => 'integer',
        'price_usd' => 'integer',
    ];

    private function getField()
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        return $columns;
    }

    public function scopeExternal($q)
    {
        $q->where('type', self::type[1]);
        // $q->whereHas('program', function ($q) {
        //     $q->where('type', 'luring');
        // });
    }

    public function scopeDaring($q)
    {
        $q->where('type', self::type[0]);
        // $q->whereHas('program', function ($q) {
        //     $q->where('type', 'daring');
        // });
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

    public function program()
    {
        return $this->belongsToMany('App\Program', 'program_ticket', 'ticket_id', 'program_id');
    }

    public function imageBanner()
    {
        return $this->hasOne('App\Image', 'relation_id', 'id')
            ->where([
                'relation_type' => 'ticket',
                'function_type' => 'banner'
            ]);
    }

    public function getPriceIdrAttribute($v)
    {
        return (int)$v;
        // if ($v !== NULL) {
        //     return number_format($v, 0, ",", ".");
        // }
        // return $v;
    }

    public function getPriceUsdAttribute($v)
    {
        return (int)$v;
        // if ($v !== NULL) {
        //     return number_format($v, 0, ",", ".");
        // }
        // return $v;
    }
}
