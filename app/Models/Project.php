<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'tbl_projects';
    protected $primaryKey = 'project_id';
    public $timestamps = false;

    public function unit(){
        return $this->hasOne(Units::class,'unit_id','unit_id');
    }
}
