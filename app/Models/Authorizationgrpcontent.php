<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authorizationgrpcontent extends Model
{
    use HasFactory;
    protected $table = 'tbl_authorization_group_content';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
