<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplatePreOpeningdocs extends Model
{
    use HasFactory;
    protected $table = 'tbl_template_preopening_docs';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
