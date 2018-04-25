<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treturndetail extends Model
{
    protected $table = 'treturndetails';

    /**
     * RELATIONSHIP
     */
    
    public function treturn()
    {
        return $this->belongsTo('App\Models\Treturn');
    }

    public function transactiondetail()
    {
        return $this->belongsTo('App\Models\Transactiondetail');
    }
}