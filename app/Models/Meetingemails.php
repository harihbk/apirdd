<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meetingemails extends Model
{
    use HasFactory;
    protected $table = 'tbl_project_meeting_emails';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
