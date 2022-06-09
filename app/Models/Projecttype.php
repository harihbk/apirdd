<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projecttype extends Model
{
    protected $table = 'tbl_projecttype_master';
    protected $primaryKey = 'type_id';
    public $timestamps = false;
}
