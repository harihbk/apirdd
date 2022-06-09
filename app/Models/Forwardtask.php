<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forwardtask extends Model
{
    use HasFactory;
    protected $table = 'tbl_task_forwards';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
