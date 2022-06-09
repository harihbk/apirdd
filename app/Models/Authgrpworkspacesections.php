<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authgrpworkspacesections extends Model
{
    use HasFactory;
    protected $table = 'tbl_authgrp_workspace_sections';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
