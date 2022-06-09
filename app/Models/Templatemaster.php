<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Templatemaster extends Model
{
    protected $table = 'tbl_template_master';
    protected $primaryKey = 'master_id';
    public $timestamps = false;
}
