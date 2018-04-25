<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opti extends Model
{
	protected $connection = 'optimize';
    protected $table = 'optimizes';
}