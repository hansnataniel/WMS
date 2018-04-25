<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pricegap extends Model
{
    protected $table = 'pricegaps';

    /**
     * RELATIONSHIP
     */
 
    public function ri()
    {
        return $this->belongsTo('App\Models\Ri');
    }
}