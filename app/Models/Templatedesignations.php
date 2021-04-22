<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Templatedesignations extends Model
{
    use HasFactory;
    protected $table = 'tbl_template_designations';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
