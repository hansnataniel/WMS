<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    /**
     * RELATIONSHIP
     */
    
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }
}