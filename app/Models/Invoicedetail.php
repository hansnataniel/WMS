<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoicedetail extends Model
{
    protected $table = 'invoicedetails';

    /**
     * RELATIONSHIP
     */
    
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }

    public function ridetail()
    {
        return $this->belongsTo('App\Models\Ridetail');
    }
}