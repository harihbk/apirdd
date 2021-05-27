<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projecttaskcomments extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_task_comments';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
