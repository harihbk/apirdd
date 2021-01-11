<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'tbl_company_master';
    protected $primaryKey = 'company_id';
    public $timestamps = false;
}
