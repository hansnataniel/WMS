<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ridetail extends Model
{
    protected $table = 'ridetails';

    /**
     * RELATIONSHIP
     */
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function ri()
    {
        return $this->belongsTo('App\Models\Ri');
    }

    public function podetail()
    {
        return $this->belongsTo('App\Models\Podetail');
    }

    public function rak()
    {
        return $this->belongsTo('App\Models\Rak');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }
}