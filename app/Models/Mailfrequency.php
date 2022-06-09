<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mailfrequency extends Model
{
    protected $table = 'tbl_mailfrequency_master';
    protected $primaryKey = 'fre_id';
    public $timestamps = false;
}
