<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectdocsApproval extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_docs_approvals';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
