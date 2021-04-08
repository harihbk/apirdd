<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authorizationgrp extends Model
{
    use HasFactory;
    protected $table = 'tbl_authorization_groups';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
