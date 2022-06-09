<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    protected $table = 'tbl_phase_master';
    protected $primaryKey = 'phase_id';
    public $timestamps = false;
}
