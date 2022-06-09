<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docpathconfig extends Model
{
    use HasFactory;
    protected $table = 'tbl_docpath_config_master';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
