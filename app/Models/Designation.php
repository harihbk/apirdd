<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $table = 'tbl_designation_master';
    protected $primaryKey = 'designation_id';
    public $timestamps = false;
}
