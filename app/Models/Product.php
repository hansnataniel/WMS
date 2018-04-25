<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    /**
     * RELATIONSHIP
     */
    
    // public function raks()
    // {
    //     return $this->belongsToMany('App\Models\Rak');
    // }

    public function kendaraan()
    {
        return $this->belongsTo('App\Models\Kendaraan');
    }

    public function transactionitems()
    {
        return $this->hasMany('App\Models\Transactionitem');
    }

    public function productphotos()
    {
        return $this->hasMany('App\Models\Productphoto');
    }

    public function inventories()
    {
        return $this->hasMany('App\Models\Inventory');
    }

    public function histories()
    {
        return $this->hasMany('App\Models\History');
    }

    public function qtydiscounts()
    {
        return $this->hasMany('App\Models\Qtydiscount');
    }
}