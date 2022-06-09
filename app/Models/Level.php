<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'tbl_member_level_master';
    protected $primaryKey = 'level_id';
    public $timestamps = false;
}
