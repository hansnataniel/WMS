<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productphoto extends Model
{
    protected $table = 'productphotos';

    /**
     * RELATIONSHIP
     */
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}