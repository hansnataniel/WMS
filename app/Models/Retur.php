<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    protected $table = 'returns';

    /**
     * RELATIONSHIP
     */
    
    public function ri()
    {
        return $this->belongsTo('App\Models\Ri');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }
}