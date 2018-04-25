<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ri extends Model
{
    protected $table = 'ris';

    /**
     * RELATIONSHIP
     */
    
    public function po()
    {
        return $this->belongsTo('App\Models\Po');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }
}