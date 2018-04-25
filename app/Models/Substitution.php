<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Substitution extends Model
{
    protected $table = 'substitutions';

    /**
     * RELATIONSHIP
     */
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}