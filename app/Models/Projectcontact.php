<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectcontact extends Model
{
    protected $table = 'tbl_project_contact_details';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
