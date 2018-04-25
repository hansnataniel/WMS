<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactiondetail extends Model
{
    protected $table = 'transactiondetails';

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function rak()
    {
        return $this->belongsTo('App\Models\Rak');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction');
    }
}