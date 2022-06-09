<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emailtemplate extends Model
{
    use HasFactory;
    protected $table = 'tbl_email_templates';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
