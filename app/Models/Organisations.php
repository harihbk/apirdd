<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisations extends Model
{
    protected $table = 'tbl_organisations_master';
    protected $primaryKey = 'org_id';
    public $timestamps = false;
}
