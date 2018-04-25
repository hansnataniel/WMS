<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Contracts\Auth\CanResetPassword;

use Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setNewPasswordAttribute($new_password)
    {
        $this->attributes['password'] = Hash::make($new_password);
    }

    /**
     * RELATIONSHIP
     */
    
    public function areas()
    {
        return $this->hasMany('App\Models\Area');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Area');
    }
}
