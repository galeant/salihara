<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const role = [
        'admin',
        'customer'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'role', 'phone', 'address',
        'is_disabled', 'gender', 'birth_year',
        'province_id', 'city_id', 'district_id',
        'sub_district_id', 'email_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'role'
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];


    private function getField()
    {
        $table = $this->getTable();
        $columns = Schema::getColumnListing($table);
        return $columns;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeCustomer($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeVerified($query)
    {
        return $query->whereNull('email_token');
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

    public function province()
    {
        return $this->belongsTo('App\Province', 'province_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo('App\City', 'city_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo('App\District', 'district_id', 'id');
    }

    public function subDistrict()
    {
        return $this->belongsTo('App\SubDistrict', 'sub_district_id', 'id');
    }

    public function access()
    {
        return $this->belongsToMany('App\Program', 'user_access', 'user_id', 'program_id');
    }

    public function transaction()
    {
        return $this->hasMany('App\Transaction', 'user_id', 'id');
    }
}
