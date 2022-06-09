<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    protected $table = 'tbl_units_master';
    protected $primaryKey = 'unit_id';
    public $timestamps = false;
}
