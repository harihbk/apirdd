<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preopeningdocs extends Model
{
    use HasFactory;
    protected $table = 'tbl_preopening_docs';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
