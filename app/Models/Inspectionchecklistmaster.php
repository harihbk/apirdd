<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspectionchecklistmaster extends Model
{
    protected $table = 'tbl_checklist_master';
    protected $primaryKey = 'ch_id';
    public $timestamps = false;
}
