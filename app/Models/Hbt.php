<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hbt extends Model
{
    protected $table = 'hbts';

    /**
     * RELATIONSHIP
     */
 
    public function ri()
    {
        return $this->belongsTo('App\Models\Ri');
    }
}