<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    protected $table = 'adjustments';

    /**
     * RELATIONSHIP
     */
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}