<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orgmilestoneconfig extends Model
{
    use HasFactory;
    protected $table = 'tbl_org_milestone_config';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
