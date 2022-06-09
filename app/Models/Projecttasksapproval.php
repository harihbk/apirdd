<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projecttasksapproval extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_tasks_approvals';
    protected $primaryKey = 'approval_id';
    public $timestamps = false;
}
