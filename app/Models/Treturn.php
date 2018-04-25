<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treturn extends Model
{
    protected $table = 'treturns';

    /**
     * RELATIONSHIP
     */
    
    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
}