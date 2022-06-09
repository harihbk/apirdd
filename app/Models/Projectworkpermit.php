<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectworkpermit extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_workpermits';
    protected $primaryKey = 'permit_id';
    public $timestamps = false;
}
