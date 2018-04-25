<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accountdetail extends Model
{
    protected $table = 'accountdetails';

    /**
     * RELATIONSHIP
     */
    
    public function account()
    {
        return $this->belongsTo('App\Models\Acc');
    }
}