<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supprice extends Model
{
    protected $table = 'supprices';

    /**
     * RELATIONSHIP
     */
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }
}