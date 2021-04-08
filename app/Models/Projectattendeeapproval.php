<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectattendeeapproval extends Model
{
    use HasFactory;
    protected $table = 'tbl_attendees_approvals';
    protected $primaryKey = 'approval_id';
    public $timestamps = false;
}
