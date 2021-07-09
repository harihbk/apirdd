<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketingteam extends Model
{
    use HasFactory;
    protected $table = 'tbl_marketing_team';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
