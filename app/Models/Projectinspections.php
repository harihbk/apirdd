<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectinspections extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_inspections';
    protected $primaryKey = 'inspection_id';
    public $timestamps = false;
}
