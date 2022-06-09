<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $table = 'tbl_floor_master';
    protected $primaryKey = 'floor_id';
    public $timestamps = false;
}
