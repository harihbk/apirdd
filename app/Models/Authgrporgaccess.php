<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authgrporgaccess extends Model
{
    use HasFactory;
    protected $table = 'tbl_authgrp_organisation_access';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
