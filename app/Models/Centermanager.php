<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centermanager extends Model
{
    use HasFactory;
    protected $table = 'tbl_center_manager';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
