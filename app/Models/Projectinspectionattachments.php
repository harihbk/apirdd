<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectinspectionattachments extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_inspection_attachments';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
