<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Program extends Model
{
    const type = [
        'daring',
        'external'
    ];
    protected $table = 'program';
    protected $guarded = [];

    private function getField()
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        return $columns;
    }

    public function scopeExternal($q)
    {
        $q->where('type', self::type[1]);
    }

    public function scopeDaring($q)
    {
        $q->where('type', self::type[0]);
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
        $column[] = 'penampil';
        if (is_array($field) && is_array($keyword)) {
            $max = count($field);
            if (count($keyword) > $max) {
                $max = count($keyword);
            }
            for ($i = 0; $i < $max; $i++) {
                if (in_array($field[$i], $column) && isset($field[$i]) && isset($keyword[$i])) {
                    if ($field[$i] == 'penampil') {
                        $q->whereHas('penampil', function ($q) use ($keyword) {
                            $q->whereIn('id', $keyword);
                        });
                    } else {
                        $q->where($field[$i], $keyword[$i]);
                    }
                }
            }
        }
    }

    public function penampil()
    {
        return $this->belongsToMany('App\Penampil', 'program_penampil', 'program_id', 'penampil_id');
    }

    public function ticket()
    {
        return $this->belongsToMany('App\Ticket', 'program_ticket', 'program_id', 'ticket_id');
    }

    public function imageBanner()
    {
        return $this->hasOne('App\Image', 'relation_id', 'id')
            ->where([
                'relation_type' => 'program',
                'function_type' => 'banner'
            ]);
    }

    public function getScheduleAttribute($v)
    {
        if ($v == NULL || $v == '') {
            return [];
        }
        return json_decode($v);
    }
}
