<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Handovercertificate extends Model
{
    use HasFactory;
    protected $table = 'tbl_handover_certificates';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
