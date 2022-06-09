<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteInspectionReport extends Model
{
    use HasFactory;
    protected $table = 'tbl_site_inspection_report';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
