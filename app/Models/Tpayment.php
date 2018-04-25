<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tpayment extends Model
{
    protected $table = 'tpayments';

    /**
     * RELATIONSHIP
     */
    
    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank');
    }
}