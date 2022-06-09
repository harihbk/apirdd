<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operationsmntteam extends Model
{
    use HasFactory;
    protected $table = 'tbl_op_maint_team';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
