<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    protected $table = 'gudangs';

    /**
     * RELATIONSHIP
     */
    
    public function raks()
    {
        return $this->hasMany('App\Models\Rak');
    }
}