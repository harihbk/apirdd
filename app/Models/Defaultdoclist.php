<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defaultdoclist extends Model
{
    protected $table = 'tbl_defaultdoclist_master';
    protected $primaryKey = 'doc_id';
    public $timestamps = false;
}
