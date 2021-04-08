<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authorizationgrpmilestone extends Model
{
    use HasFactory;
    protected $table = 'tbl_authorization_group_milestones';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
