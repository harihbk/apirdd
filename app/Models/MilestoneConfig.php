<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilestoneConfig extends Model
{
    use HasFactory;
    protected $table = 'tbl_milestone_config_master';
    protected $primaryKey = 'config_id';
    public $timestamps = false;
}
