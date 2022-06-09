<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectinvestordates extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_investor_planned_dates';
    protected $primaryKey = 'date_id';
    public $timestamps = false;
}
