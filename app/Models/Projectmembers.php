<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectmembers extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_members';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
