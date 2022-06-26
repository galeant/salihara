<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Misc extends Model
{
    protected $table = 'misc';
    protected $guarded = [];

    public function imageBanner()
    {
        return $this->hasOne('App\Image', 'relation_id', 'id')
            ->where([
                'relation_type' => NULL,
                'function_type' => 'about'
            ]);
    }
}
