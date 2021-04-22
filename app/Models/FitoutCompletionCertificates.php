<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FitoutCompletionCertificates extends Model
{
    use HasFactory;
    protected $table = 'tbl_fitout_completion_certificates';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
