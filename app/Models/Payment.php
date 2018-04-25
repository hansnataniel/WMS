<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    /**
     * RELATIONSHIP
     */
    
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank');
    }
}