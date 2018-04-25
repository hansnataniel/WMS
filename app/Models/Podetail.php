<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Podetail extends Model
{
    protected $table = 'podetails';

    /**
     * RELATIONSHIP
     */
    
    public function po()
    {
        return $this->belongsTo('App\Models\Po');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}