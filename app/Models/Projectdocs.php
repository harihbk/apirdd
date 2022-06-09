<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projectdocs extends Model
{
    protected $table = 'tbl_projecttasks_docs';
    protected $primaryKey = 'doc_id';
    public $timestamps = false;
}
