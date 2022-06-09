<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspectionroot extends Model
{
    protected $table = 'tbl_inspection_root_categories';
    protected $primaryKey = 'root_id';
    public $timestamps = false;
}
