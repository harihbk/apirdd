<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workpermit extends Model
{
    protected $table = 'tbl_workpermit_master';
    protected $primaryKey = 'permit_id';
    public $timestamps = false;
}
