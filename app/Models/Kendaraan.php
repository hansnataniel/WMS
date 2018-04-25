<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'kendaraans';

    /**
     * RELATIONSHIP
     */
    
    public function services()
    {
        return $this->hasMany('App\Models\Product');
    }
}