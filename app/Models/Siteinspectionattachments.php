<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siteinspectionattachments extends Model
{
    use HasFactory;
    protected $table = 'tbl_site_inspection_attachments';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
