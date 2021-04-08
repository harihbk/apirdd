<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectmilestonedates extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_milestone_dates';
    protected $primaryKey = 'date_id';
    public $timestamps = false;
}
