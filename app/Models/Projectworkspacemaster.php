<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectworkspacemaster extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_workspace_master';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
