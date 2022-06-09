<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projecttemplate extends Model
{
    protected $table = 'tbl_project_template';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
