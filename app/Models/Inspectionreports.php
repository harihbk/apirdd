<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspectionreports extends Model
{
    use HasFactory;
    protected $table = 'tbl_inspection_reports';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
