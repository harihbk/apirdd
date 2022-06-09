<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectworkspacesections extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_workspace_sections';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
