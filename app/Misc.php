<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Misc extends Model
{
    protected $table = 'misc';
    protected $guarded = [];

    public function aboutImageBanner()
    {
        return $this->hasOne('App\Image', 'relation_id', 'id')
            ->where([
                'relation_type' => NULL,
                'function_type' => 'about'
            ]);
    }

    public function mainImageBanner()
    {
        return $this->hasOne('App\Image', 'relation_id', 'id')
            ->where([
                'relation_type' => NULL,
                'function_type' => 'main_banner'
            ]);
    }

    public function scopeAbout($query)
    {
        return $query->where('segment', 'about');
    }

    public function scopeMainBanner($query)
    {
        return $query->where('segment', 'main_banner');
    }

    public function scopeCommittee($query)
    {
        return $query->where('segment', 'committee');
    }
}
