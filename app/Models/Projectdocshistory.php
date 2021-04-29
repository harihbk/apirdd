<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectdocshistory extends Model
{
    use HasFactory;
    protected $table = 'tbl_projectdocs_history';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
