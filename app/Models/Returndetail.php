<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Returndetail extends Model
{
    protected $table = 'returndetails';

    /**
     * RELATIONSHIP
     */
    
    public function retur()
    {
        return $this->belongsTo('App\Models\Retur');
    }

    public function ridetail()
    {
        return $this->belongsTo('App\Models\Ridetail');
    }
}