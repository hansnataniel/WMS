<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';

    /**
     * RELATIONSHIP
     */
    
    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }
}