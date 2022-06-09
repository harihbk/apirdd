<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintainenceteam extends Model
{
    use HasFactory;
    protected $table = 'tbl_maintainence_team';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
