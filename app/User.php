<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'role', 'phone', 'address',
        'is_disabled'
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
}
