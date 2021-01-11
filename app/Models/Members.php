<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'mem_id';
    public $timestamps = false;
}
