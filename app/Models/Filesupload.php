<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filesupload extends Model
{
    use HasFactory;
    protected $table = 'tbl_files_upload';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
