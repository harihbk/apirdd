<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    protected $table = 'tbl_properties_master';
    protected $primaryKey = 'property_id';
    public $timestamps = false;
}
