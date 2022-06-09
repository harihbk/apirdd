<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Templatenamemaster extends Model
{
    use HasFactory;
    protected $table = 'tbl_templatename_master';
    protected $primaryKey = 'template_id';
    public $timestamps = false;
}
