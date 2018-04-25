<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productstock extends Model
{
    protected $table = 'productstocks';

    /**
     * RELATIONSHIP
     */
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function rak()
    {
        return $this->belongsTo('App\Models\Rak');
    }
}