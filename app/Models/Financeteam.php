<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financeteam extends Model
{
    use HasFactory;
    protected $table = 'tbl_finance_team';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
