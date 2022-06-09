<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklisttemplate extends Model
{
    protected $table = 'tbl_checklisttemplate_master';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
