<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    protected $table = 'raks';

    /**
     * RELATIONSHIP
     */
    
    // public function products()
    // {
    //     return $this->belongsToMany('App\Models\Product');
    // }

    public function gudang()
    {
        return $this->belongsTo('App\Models\Gudang');
    }
}