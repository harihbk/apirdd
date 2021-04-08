<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authorizationgrpmaster extends Model
{
    use HasFactory;
    protected $table = 'tbl_authorisation_content_master';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
